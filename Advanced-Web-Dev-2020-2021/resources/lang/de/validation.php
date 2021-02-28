<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute muss akzeptiert werden.',
    'active_url' => ':attribute ist keine gültige URL.',
    'after' => ':attribute muss ein Datum nach dem :date sein.',
    'after_or_equal' => ':attribute muss ein Datum nach oder gleich dem :date sein.',
    'alpha' => ':attribute darf nur Buchstaben enthalten.',
    'alpha_dash' => ':attribute darf nur Buchstaben, Zahlen, Bindestriche und Unterstriche enthalten.',
    'alpha_num' => ':attribute darf nur Buchstaben und Zahlen enthalten.',
    'array' => ':attribute muss eine Liste sein.',
    'before' => ':attribute muss ein Datum vor dem :date sein.',
    'before_or_equal' => ':attribute muss ein Datum vor oder gleich dem :date sein.',
    'between' => [
        'numeric' => ':attribute muss zwischen :min und :max liegen.',
        'file' => ':attribute muss zwischen :min und :max Kilobytes liegen.',
        'string' => ':attribute muss zwischen :min und :max Zeichen enthalten.',
        'array' => ':attribute muss zwischen :min und :max Einträge enthalten.',
    ],
    'boolean' => 'Das :attribute Feld muss richtig oder falsch sein.',
    'confirmed' => 'Die :attribute Bestätigung stimmt nicht überein.',
    'date' => ':attribute ist kein gültiges Datum.',
    'date_equals' => ':attribute muss gleich dem :date sein.',
    'date_format' => ':attribute stimmt nicht mit dem Format :format überein.',
    'different' => ':attribute und :other müssen unterschiedlich sein.',
    'digits' => ':attribute muss :digits Ziffern enthalten.',
    'digits_between' => ':attribute muss zwischen :min und :max Ziffern enthalten.',
    'dimensions' => ':attribute hat ungültige Bild Dimensionen.',
    'distinct' => 'Das :attribute Feld einen Wert mehrfach.',
    'email' => ':attribute muss eine gültige Email-Adresse sein.',
    'ends_with' => ':attribute muss mit einem der Folgenden enden: :values.',
    'exists' => 'Das ausgewählte :attribute ist ungültig.',
    'file' => ':attribute muss eine Datei sein.',
    'filled' => 'Das :attribute Feld muss ausgefüllt sein.',
    'gt' => [
        'numeric' => ':attribute muss größer als :value sein.',
        'file' => ':attribute muss größer als :value Kilobytes sein.',
        'string' => ':attribute muss länger als :value Zeichen sein.',
        'array' => ':attribute muss mehr als :value Einträge enthalten.',
    ],
    'gte' => [
        'numeric' => ':attribute muss mindestens :value sein.',
        'file' => ':attribute muss mindestens :value Kilobytes sein.',
        'string' => ':attribute muss mindestens :value Zeichen lang sein.',
        'array' => ':attribute muss mindestens :value Einträge enthalten.',
    ],
    'image' => ':attribute muss ein Bild sein.',
    'in' => 'Das ausgewählte :attribute ist ungültig.',
    'in_array' => 'Das :attribute Feld existiert nicht in :other.',
    'integer' => ':attribute muss ein Integer sein.',
    'ip' => ':attribute muss eine gültige IP-Adresse sein.',
    'ipv4' => ':attribute muss eine gültige IPv4-Addresse.',
    'ipv6' => ':attribute muss eine gültige IPv6-Addresse.',
    'json' => ':attribute muss ein gültiger JSON-String sein.',
    'lt' => [
        'numeric' => ':attribute muss kleiner als :value sein.',
        'file' => ':attribute muss kleiner als :value Kilobytes sein.',
        'string' => ':attribute muss kürzer als :value Zeichen sein.',
        'array' => ':attribute muss weniger als :value Einträge enthalten.',
    ],
    'lte' => [
        'numeric' => ':attribute darf maximal :value sein.',
        'file' => ':attribute darf maximal :value kilobytes sein.',
        'string' => ':attribute darf maximal :value Zeichen enthalten.',
        'array' => ':attribute darf maximal :value Einträge enthalten.',
    ],
    'max' => [
        'numeric' => ':attribute darf nicht größer als :max sein.',
        'file' => ':attribute darf nicht größer als :max Kilobytes sein.',
        'string' => 'Der :attribute darf nicht mehr als :max Zeichen enthalten.',
        'array' => ':attribute darf nicht ehr als :max Einträge enthalten.',
    ],
    'mimes' => ':attribute muss eine Datei vom Typ :values sein.',
    'mimetypes' => ':attribute muss eine Datei vom Typ :values sein.',
    'min' => [
        'numeric' => ':attribute muss mindestens :min sein.',
        'file' => ':attribute muss mindestens :min Kilobytes sein.',
        'string' => ':attribute muss mindestens :min Zeichen enthalten.',
        'array' => ':attribute muss mindestens :min Einträge enthalten.',
    ],
    'not_in' => 'Das ausgewählte :attribute ist ungültig.',
    'not_regex' => 'Das :attribute Format ist ungültig.',
    'numeric' => ':attribute muss eine Zahl sein.',
    'password' => 'Das Passwort ist inkorrekt.',
    'present' => 'Das :attribute Feld muss vorhanden sein.',
    'regex' => 'Das :attribute Format ist ungültig.',
    'required' => 'Das :attribute Feld wird benötigt.',
    'required_if' => 'Das :attribute Feld wird benötigt wenn :other :value beträgt.',
    'required_unless' => 'Das :attribute Feld wird benötigt solange :other nicht in :values vorhanden ist.',
    'required_with' => 'Das :attribute Feld wird benötigt wenn :values vorhanden ist.',
    'required_with_all' => 'Das :attribute Feld wird benötigt wenn :values vorhanden sind.',
    'required_without' => 'Das :attribute Feld wird benötigt wenn :values nicht vorhanden ist.',
    'required_without_all' => 'Das :attribute Feld wird benötigt wenn keine von :values vorhanden sind.',
    'same' => ':attribute und :other müssen übereinstimmen.',
    'size' => [
        'numeric' => ':attribute muss :size groß sein.',
        'file' => ':attribute muss :size Kilobytes groß sein.',
        'string' => ':attribute muss :size Zeichen enthalten.',
        'array' => ':attribute muss :size Einträge enthalten.',
    ],
    'starts_with' => ':attribute muss mit einem der Folgenden beginnen: :values.',
    'string' => ':attribute muss ein String sein.',
    'timezone' => ':attribute muss eine gültige Zeitzone sein.',
    'unique' => 'Die :attribute wird bereits verwendet.',
    'uploaded' => ':attribute schlug beim hochladen fehl.',
    'url' => 'Das :attribute Format ist ungültig.',
    'uuid' => ':attribute muss eine gültige UUID sein.',
    'department_failed' => 'Ungültige Abteilung zugewiesen.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'email' => 'Email Adresse',
        'verifypassword' => 'Passwortwiederholung',
        'password' => 'Passwort',
        'name' => 'Vorname',
        'lastname' => 'Nachname',
        'problemtext' => 'Problemtext',
    ],

];
