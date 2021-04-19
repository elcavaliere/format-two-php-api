<?php

namespace Database\Factories;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Lang;

class transactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        $value = rand(-1000, 1000);

        return [
            'created_at' => Carbon::now()->subMinutes(rand(0, 10000)),
            'value' => $value,
            'type' => $value < 0 ? Transaction::TYPE_DEBIT : Transaction::TYPE_REFILL,
            'description' => $this->getDescription($value)
        ];
    }

    /**
     * @param $value
     * @return string
     */
    protected function getDescription($value) : string
    {
        $text = $value < 0 ? 'Списание' : 'Пополнение';
        $text .= ' на сумму ' . $value . ' '  . Lang::choice('рубль|рубля|рублей', $value, [], 'ru');

        return $text;
    }
}
