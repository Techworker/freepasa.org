# freepasa.api

Simple API for wallets to request accounts without

## Preparation

Get yourself an API key from the hoster of the project. The API key will allow you to use the API.

## Workflow

There are 2 endpoints to talk to.

 - `https://domain/api/app/request.php`
   Requests a new account and will send a verification code via SMS
 - `https://domain/api/app/code.php`
   Tries to verify the SMS code and send a PASA on success.
   
The api will always respond in the same format:

```
{
    "request_id": null or a short unique cryptic looking string "bQnSoqwW34",
    "data": {} or [],
    "status": "error" or "pending" or "success"
``` 

The `request_id` will remain `null` until a valid request is made.

The `status` lets you identify the response type.

It can be:
 - `error` in case any error occured (missing/invalid params)
 - `success` is only returned after you successfully obtained a PASA.
 - `pending` is only returned when there is a pending and ongoing request.

The `data` field will either contain information about a successfully obtained pasa
or a single value identifying an error.

The errors need to be formulated on the requesting side, the API will only return error
identifiers.

### Request a new account

Make a GET request to: `https://domain/api/app/request.php` using the following parameters:

 - `api_key` The API key key you got.
 - `phone_iso` An ISO 3166 code of the phone number. 
 - `phone_number` The phone number without country identifier.
 - `public_key` The base58 publick key to request an account for.

The API will wither respond with an error or a new `request_id` that you can use in the next
request to identify your previous request.

The following error codes can be returned (`"status" = "error"`)

 - `missing_api_key` You forgot to send the API key via `api_key`.
 - `wrong_api_key` Your API key is invalid.
 - `missing_phone_iso` Missing the `phone_iso` parameter.
 - `invalid_phone_iso` The `phone_iso` parameter is wrong.
 - `missing_public_key` Missing the `public_key` parameter.
 - `missing_phone_number` Missing the `phone_number` parameter.
 - `invalid_phone_number` The given phone number is invalid.
 - `public_key_already_used` The public key was already used before.
 - `public_key_has_accounts` The pubkey has 1 or more accounts.
 - `invalid_public_key` The public key is wrong.
 - `already_disbursed` The public key was already used. In this case you will get the request_id.
 - `unknown` An unknown error occured.

If your request was successful, a response in the following format will be send:

``` json
{
  "status": "success",
  "data": {},
  "request_id": "Hsdkjndjks"
}
```

If not, it will look like this:

``` json
{
  "status": "error",
  "data": ["missing_api_key"],
  "request_id": null
}
```

If the request is active but not verified you will get this response:

``` json
{
  "status": "pending",
  "data": {},
  "request_id": "dnfsja"
}


```


### Verify the SMS code

Make a GET request to: `https://domain/api/app/code.php` using the following parameters:

 - `api_key` The API key key you got.
 - `request_id` The request ID you got from the previous request. 
 - `code` The SMS code.

The API will either respond with an error or the ophash + the account of the disbursed pasa.

The following error codes can be returned (`"status" = "error"`)

 - `missing_api_key` You forgot to send the API key via `api_key`.
 - `wrong_api_key` Your API key is invalid.
 - `missing_request_id` You did not send the request id.
 - `missing_code` You did not send the code.
 - `invalid_request_id` The given request id is invalid.
 - `error_too_many_tries` More than 3 code verification tries.
 - `verification_failed` The verification was not successful.
 
If your request was successful, a response in the following format will be send:

``` json
{
  "status": "success",
  "data": {
    "account": 123456,
    "ophash": "ABCDE...",
    "link": "https://domain/success.php?id=request_id"
  },
  "request_id": "Hsdkjndjks"
}
```

If not, it will look like this:

``` json
{
  "status": "error",
  "data": ["missing_api_key"],
  "request_id": "Hsdkjndjks"
}
```
