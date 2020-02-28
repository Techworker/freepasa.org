# freepasa trusted exchange api

Simple API for exchanges to request a PASA

## Preparation

Get yourself an API key from the hoster of the project. The API key will allow you to use the API.

## Workflow

There is 1 endpoint to talk to.

 - `https://domain/api/exchange.php`
   Requests a new account and will send it to the given public key.
   
The api will always respond in the same format:

```
{
    "request_id": null or a short unique cryptic looking string "bQnSoqwW34",
    "data": {} or [],
    "status": "error" or "pending" or "success"
``` 

It can be:
 - `error` in case any error occured (missing/invalid params)
 - `success` is only returned after you successfully obtained a PASA.

The `data` field will either contain information about a successfully obtained pasa
or a single value identifying an error.

The errors need to be formulated on the requesting side, the API will only return error
identifiers.

### Request a new account

Make a GET request to: `https://domain/api/exchange.php` using the following parameters:

 - `api_key` The API key key you got.
 - `origin` An identifier you get with your API key. 
 - `user_id` A number identifying your user.
 - `public_key` The base58 public key to request an account for.

The API will either respond with an error or a success message including the account
and the operation hash.

Requests are idempotent. A PASA can only be distributed if the public key
and the user id was not used before.

The following error codes can be returned (`"status" = "error"`)

 - `missing_origin` - Missing origin value
 - `invalid_origin` - Invalid origin value
 - `missing_api_key` - Missing API key
 - `invalid_api_key` - Invalid API key
 - `missing_user_id` - Missing user id.
 - `user_id_should_be_an_int32_gt_0` - invalid user id, must be int.
 - `missing_public_key` - missing the public key value.
 - `public_key_has_accounts` - public key has accounts associated.
 - `invalid_public_key` - Public key is invalid.

If your request was successful, a response in the following format will be send:

``` json
{
  "status": "success",
  "data": {
     "account": "123-12",
     "ophash": "abc",
     "link": "explorer-link"
  },
  "request_id": "custom state id"
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
