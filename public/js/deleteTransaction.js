function deleteTransactionEntry(transactionType, transactionId, callback) {
    console.log('No i wyszło: ', transactionType, transactionId);
    let url = "/" + transactionType + "s/deleteEntryAjax";
    $.ajax({
        url: url,
        type: "POST",
        dataType: 'json',
        cache: false,
        data: { id: transactionId }
    }).done(function (response) {
        console.log(response);
        callback(response);

    }).fail(function (response) {
        console.log("No i klops!" + response);
        console.dir(arguments);
    })
}