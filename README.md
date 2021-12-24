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

<br><br>
1. Generate mnemonic phrase<br><br>
$mnemonic = Uwim::GenerateMnemonic();<br><br>
To generate public, private keys or addresses from a mnemonic phrase, you can use a ready-made mnemonic phrase. 

2. Generating a Seed string from the mnemonic phrase <br><br>
$seed = Uwim::SeedFromMnemonic($mnemonic);

3. Generating a secret key from a Seed string or mnemonic phrase <br><br>
$secret_key = Uwim::SecretKeyFromSeed($seed);<br><br>
$secret_key = Uwim::SecretKeyFromMnemonic($mnemonic);

4. Generation of a public key from a secret one or mnemonic phrase <br><br>
$public_key = Uwim::PublicKeyFromSecretKey($secret_key);<br><br>
$public_key = Uwim::PublicKeyFromMnemonic($mnemonic);
  
5. A user address generation from a public key or mnemonic phrase<br><br>
You can use a public key or a mnemonic phrase to generate an address. You must also specify one of the three available prefixes. If you specify any other prefix, the function will return an error<br><br>
5.1 Generating an address with a prefix "uw" - user wallet address<br><br>
$uw_address = Uwim::AddressFromPublicKey($public_key, "uw");<br><br>
$uw_address = Uwim::AddressFromMnemonic($mnemonic, "uw");<br><br>
5.2 Generating an address with the "sc" prefix - smart contract address<br><br>
$sc_address = Uwim::AddressFromPublicKey($public_key, "sc");<br><br>
$sc_address = Uwim::AddressFromMnemonic($mnemonic, "sc");<br><br>
5.3 Generating an address with the "nd" prefix - the address of the node<br><br>
$nd_address = Uwim::AddressFromPublicKey($public_key, "nd");<br><br>
$nd_address = Uwim::AddressFromMnemonic($mnemonic, "nd");<br><br>

6. Receiving a RAW transaction line for sending to the blockchain API 
In order to generate a RAW transaction line, you need to specify the following data as: 
Mnemonic phrase (the sender of the transaction); 
Sender address (must be generated from a mnemonic phrase or be suitable for it); 
Address of the recipient; 
The number of coins you want to transfer (for some transaction types or transaction subtypes, the number of coins may be zero early); 
Address of the recipient; 
The designation of the token whose coins you want to transfer (for example: "uwm"); 
Transaction subtype (for example: "default_transaction"); 
Transaction comment data in JSON format (for each type or subtype of a transaction, its own comment data is indicated or not indicated at all); 
Transaction type (Number 1 or 3); <br><br>
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
