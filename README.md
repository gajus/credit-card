# Credit Card Validation Class

Simple class used to validate Visa, AmEx, MasterCard and Maestro cards.

## Skipping the Validation Altogether

If your vendor does not charge you for failed attempts to process a transaction, do not attempt to validate the credit card details – leave the work to the payment gateway. Otherwise, you are risking to decline perfectly valid transactions. I have talked about similar issue in the [How do you detect Credit card type based on number?](http://stackoverflow.com/a/22034170/368691) stackoverflow question.
