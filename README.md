# uwim.lib.php

1. Генерация мнемонической фразы<br><br>
$mnemonic = Uwim::GenerateMnemonic();<br><br>
Для генерации публичного, секретного ключей или адреса из мнемонической фразы можно использовать готовую мнемофразу.

2. Генерация Seed строки из мнемонической фразы<br><br>
$seed = Uwim::SeedFromMnemonic($mnemonic);

3. Генерация секретного ключа из Seed строки или мнемонической фразы<br><br>
$secret_key = Uwim::SecretKeyFromSeed($seed);<br><br>
$secret_key = Uwim::SecretKeyFromMnemonic($mnemonic);

4. Генерация публичного из секретного ключа или мнемонической фразы<br><br>
$public_key = Uwim::PublicKeyFromSecretKey($secret_key);<br><br>
$public_key = Uwim::PublicKeyFromMnemonic($mnemonic);
  
5. Генерация адреса пользователя из публичного ключа или мнемонической фразы<br><br>
Для генерации адреса можно использовать публичный ключ или мнемоническую фразу, а также необходимо указать один из трёх доступных префиксов, если вы укажите какой-либо другой префикс, то функция вернёт ошибку<br><br>
5.1 Генерация адреса с префиксом "uw" - адрес кошелька пользователя<br><br>
$uw_address = Uwim::AddressFromPublicKey($public_key, "uw");<br><br>
$uw_address = Uwim::AddressFromMnemonic($mnemonic, "uw");<br><br>
5.2 Генерация адреса с префиксом "sc" - адрес смарт-контракта<br><br>
$sc_address = Uwim::AddressFromPublicKey($public_key, "sc");<br><br>
$sc_address = Uwim::AddressFromMnemonic($mnemonic, "sc");<br><br>
5.3 Генерация адреса с префиксом "nd" - адрес ноды<br><br>
$nd_address = Uwim::AddressFromPublicKey($public_key, "nd");<br><br>
$nd_address = Uwim::AddressFromMnemonic($mnemonic, "nd");<br><br>

6. Получение RAW строки транзакции для отправки в API блокчейна<br><br>
Для того, чтобы сгенерировать RAW строку транзакции, вам необходимо указать такие данные как:<br>
Мнемоническая фраза (отправителя транзакции);<br>
Адрес отправителя (должен быть сгенерирован из мнемонической фразы или же подходить к ней);<br>
Адрес получателя;<br>
Количество монет, которое вы хотите перевести (для некоторых типов транзакции или подтипов транзакции, количество монет может быть рано нулю);<br>
Адрес получателя;<br>
Обозначение токена, монеты которого вы хотите перевести (например: "uwm");<br>
Подтип пранзакции (например: "default_transaction");<br>
Данные комментария к транзакции в формате JSON(для каждого типа или подтипа транзакции указываются свои данные комметрария или же не указываются совсем);<br>
Тип пранзакции (Число 1 или 3);<br><br>
$transaction_raw = Uwim::GetRawTransaction(
    $mnemonic,
    $sender_address,
    $recipient_addres,
    $amount,
    $token_label,
    $transaction_comment_title,
    $transaction_comment_data,
    $transaction_type
);
