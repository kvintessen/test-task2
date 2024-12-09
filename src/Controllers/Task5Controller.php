<?php

declare(strict_types=1);

namespace App\Controllers;

class Task5Controller
{
    public function handle()
    {
        $JS = <<<JS
function printOrderTotal(responseString) {
    try {
        const responseJSON = JSON.parse(responseString);
        
        if (!Array.isArray(responseJSON)) {
            console.error('Некорректный формат данных. Ожидается массив объектов.');
            
            return;
        }
        
        const orderSubtotal = responseJSON.reduce((subtotal, item) => {
            const price = item.price !== undefined ? item.price : 0;
            
            return subtotal + price;
        }, 0);
        
        console.log(
            orderSubtotal > 0
                ? `Стоимость заказа: \${orderSubtotal} руб.`
                : 'Стоимость заказа: Бесплатно'
        );
    } catch (error) {
        console.error('Ошибка обработки ответа: ', error.message);
    }
}
JS;
        echo "<pre>$JS</pre>";
    }
}