jQuery.extend(jQuery.validator.messages, {
    required: "To pole jest wymagane.",
    email: "Proszę podać prawidłowy adres e-mail.",
    date: "Należy podać datę w formacie YYYY-MM-DD.",
    dateISO: "Należy podać datę w formacie YYYY-MM-DD.",
    number: "Należy podać liczbę. Miejsca dziesiętne oddzielone kropką.",
    step: "Proszę podać liczbę w zaokrągleniu do 2 miejsc po kropce.",
    digits: "To pole akceptuje jedynie liczby oddzielone kropką.",
    maxlength: jQuery.validator.format("Maksymalna ilość znaków to {0}."),
    minlength: jQuery.validator.format("Minimalna ilość znaków to {0}."),
    rangelength: jQuery.validator.format("Należy podać od {0} do {1} znaków."),
    range: jQuery.validator.format("Należy podać od {0} do {1} znaków."),
    max: jQuery.validator.format("Należy podać wartość mniejszą lub równą {0}."),
    min: jQuery.validator.format("Należy podać wartość większą lub równą {0}.")
});