$(document).ready(function () {
    $('input[name^="note"], input[name^="commentaire"]').on('input', function () {
        $('.submitNoteButton').prop('disabled', false);
    });
});
$(document).ready(function () {
    $('.detailsEval form :input').on('input', function () {
        $('.submitEvalButton').prop('disabled', false);
    });
});

