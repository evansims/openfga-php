# DefaultMessagesInterface

Interface for mapping exception error enums to their default message keys. Provides a contract for retrieving default translation message keys for various types of exceptions in the OpenFGA SDK. This interface enables consistent error messaging across the SDK by centralizing the mapping between error types and their corresponding human-readable messages, supporting internationalization and localization. The interface handles all major error categories in the OpenFGA SDK: authentication, client, configuration, network, and serialization errors. Each method returns a Messages enum that can be used with the translation system to generate localized error messages.

## Namespace
`OpenFGA\Exceptions`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/DefaultMessagesInterface.php)

## Related Classes
* [DefaultMessages](Exceptions/DefaultMessages.md) (implementation)
