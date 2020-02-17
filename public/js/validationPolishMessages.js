jQuery.extend(jQuery.validator.messages, {
    required: "To pole jest wymagane.",
    remote: "Please fix this field.",
    email: "Proszę podać prawidłowy adres e-mail.",
    date: "Należy podać datę w formacie YYYY-MM-DD.",
    dateISO: "Należy podać datę w formacie YYYY-MM-DD.",
    number: "Należy podać liczbę. Miejsca dziesiętne oddzielone przecinkiem.",
    step: "Proszę podać liczbę w zaokrągleniu do 2 miejsc po przecinku.",
    digits: "To pole akceptuje jedynie liczby oddzielone przecinkiem (',').",
    maxlength: jQuery.validator.format("Maksymalna ilość znaków to {0}."),
    minlength: jQuery.validator.format("Minimalna ilość znaków to {0}."),
    rangelength: jQuery.validator.format("Please enter a value between {0} and {1} characters long."),
    range: jQuery.validator.format("Please enter a value between {0} and {1}."),
    max: jQuery.validator.format("Please enter a value less than or equal to {0}."),
    min: jQuery.validator.format("Please enter a value greater than or equal to {0}.")
});