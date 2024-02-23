<?php

declare(strict_types=1);

namespace App\Gpt\OpenAI\Transformers;

use App\Common\Structures\City;
use App\Common\Structures\Coordinates;
use App\Common\Structures\DateTimeRange;
use App\Common\Structures\TimeOfDay;
use App\Gpt\Exceptions\UnableToParseQuestion;
use App\Gpt\OpenAI\Structures\OpenAIChatCompletion;
use App\Gpt\ReservationRequest;

final class OpenAIReservationAnswerTransformer
{
    public static function transform(OpenAIChatCompletion $structure): ReservationRequest
    {
        /**
         * We can only have 1 option
         *
         * @see OpenAIDialogueRequest::$maxResponseChatCompletionsCount
         */
        $choice = $structure->choices[0];

        return match ($choice->message->content) {
            'unknown' => throw new UnableToParseQuestion('The requested action could not be recognized'),
            'unknown:criteria' => throw new UnableToParseQuestion('Please enter the correct city and desired date'),
            default => self::buildModel($choice->message->content)
        };
    }

    private static function buildModel(string $content): ReservationRequest
    {
        $stringParts = \explode('|', $content);

        $timeOfDay = TimeOfDay::from(\strtolower(!empty($stringParts[6]) ? $stringParts[6] : 'any'));
        $datetimeParts = \explode(' to ', $stringParts[5]);

        return new ReservationRequest(
            city: new City(
                name: $stringParts[2],
                coordinates: new Coordinates(
                    latitude: $stringParts[3],
                    longitude: $stringParts[4]
                )
            ),
            personsCount: (int)$stringParts[1],
            dateTimeRange: self::buildTimeRange(
                fromString: $datetimeParts[0],
                toString: $datetimeParts[1] ?? null,
                desiredTimeOfDay: $timeOfDay
            )
        );
    }

    public static function buildTimeRange(string $fromString, ?string $toString, TimeOfDay $desiredTimeOfDay): DateTimeRange
    {
        $from = self::createDateTime($fromString);
        $to = self::createDateTime(($toString ?? $fromString));

        switch ($desiredTimeOfDay) {
            case TimeOfDay::MORNING:
                $from->setTime(6, 0);
                $to->setTime(12, 0);
                break;
            case TimeOfDay::LUNCH:
                $from->setTime(12, 0);
                $to->setTime(18, 0);
                break;
            case TimeOfDay::EVENING:
                $from->setTime(18, 0);
                $to->setTime(23, 59, 59);
                break;
            case TimeOfDay::NIGHT:
                $from->setTime(23, 0);
                $to->modify('+1 day');
                $to->setTime(4, 0);
                break;
            default:
                $from->setTime(0, 0);
                $to->setTime(23, 59, 59);
        }

        return new DateTimeRange(
            \DateTimeImmutable::createFromMutable($from),
            \DateTimeImmutable::createFromMutable($to),
        );
    }

    private static function createDateTime(string $dateString): \DateTime
    {
        try {
            return new \DateTime(\sprintf('@%s', \strtotime($dateString)));
        } catch (\Exception) {
            throw new UnableToParseQuestion(
                \sprintf('Unexpected response: unable to parse date `%s`', $dateString)
            );
        }
    }
}
