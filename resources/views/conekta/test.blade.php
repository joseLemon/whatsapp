@extends('master')
@section('css')
    <style>
        input {
            margin: 5px 0;
        }

        input:not([type=submit]) {
            width: 100%;
        }

        input[type=submit] {
            width: 100px;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <form action="">
            <div class="row">
                <div class="col-12">
                    <input type="text" class="input-group-text" name="c_holder_name" id="c_holder_name"
                           placeholder="Nombre">
                    <input type="email" class="input-group-text" name="c_holder_email" id="c_holder_email"
                           placeholder="Email">
                </div>
                <div class="col-12">
                    <input type="text" class="input-group-text" name="c_number" id="c_number"
                           placeholder="Número de tarjeta">
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <input type="text" class="input-group-text" name="c_exp_year" id="c_exp_year" placeholder="MM/AAAA">
                </div>
                <div class="col-6">
                    <input type="text" class="input-group-text" name="c_cvc" id="c_cvc" placeholder="CVC">
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center"><input class="mx-auto" type="submit" value="Pagar"></div>
            </div>
        </form>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript" src="https://cdn.conekta.io/js/latest/conekta.js"></script>
    <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
    <script>
        let cleave_exp_year = new Cleave('#c_exp_year', {
                date: true,
                datePattern: ['m', 'Y']
            }),
            cleave_card_number = new Cleave('#c_number', {
                creditCard: true,
            });

        Conekta.setPublicKey("key_PL3QPzUWjrpsHAhMNAPjtZw");
        Conekta.setLanguage("es");

        let successResponseHandler = (token) => {
                console.log(token)
            },
            errorResponseHandler = (error) => {
                alert(error.message_to_purchaser);
            },
            tokenParams = {},
            validateCardData = () => {
                let val = true,
                    errors = '';

                // Validate card number
                if (!Conekta.card.validateNumber(tokenParams.card.number)) {
                    val = !val;
                    errors += 'El número de tarjeta ingresado no tiene un formato válido.\n';
                }
                // Validate expiration date
                if (!Conekta.card.validateExpirationDate(tokenParams.card.exp_month, tokenParams.card.exp_year)) {
                    if (val)
                        val = !val;
                    errors += 'La fecha de expiración no es válida.\n';
                }
                // Validate CVC
                if (!Conekta.card.validateCVC(tokenParams.card.cvc)) {
                    if (val)
                        val = !val;
                    errors += 'El código de seguridad (CVC) no es válido.\n';
                }
                // Notify errors if there's any
                if (errors !== '') {
                    alert(errors);
                }
                // Return validation result
                return val;
            },
            generateToken = () => {
                if (validateCardData())
                    Conekta.Token.create(tokenParams, successResponseHandler, errorResponseHandler);
            };

        $('form').submit(function (e) {
            e.preventDefault();
            let exp = $('#c_exp_year').val().split('/');
            tokenParams = {
                "card": {
                    "number": $('#c_number').val(), // Datos mínimos requridos
                    "name": $('#c_holder_name').val(), // Datos mínimos requridos
                    "exp_year": exp[1], // Datos mínimos requridos
                    "exp_month": exp[0], // Datos mínimos requridos
                    "cvc": $('#c_cvc').val(), // Datos mínimos requridos
                }
            };
            generateToken();
        });

        /* EXAMPLE OF TOKEN PARAMS
        tokenParams = {
            "card": {
                "number": "4242424242424242", // Datos mínimos requridos
                "name": "Fulanito Pérez", // Datos mínimos requridos
                "exp_year": "2020", // Datos mínimos requridos
                "exp_month": "12", // Datos mínimos requridos
                "cvc": "123", // Datos mínimos requridos
                "address": {
                    "street1": "Calle 123 Int 404",
                    "street2": "Col. Condesa",
                    "city": "Ciudad de Mexico",
                    "state": "Distrito Federal",
                    "zip": "12345",
                    "country": "Mexico"
                }
            }
        };*/
    </script>
@endsection