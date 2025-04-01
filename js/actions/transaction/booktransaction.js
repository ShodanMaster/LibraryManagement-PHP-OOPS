$(document).ready(function () {
    
    $(document).on('click', '#add-transaction-grid',function (e) {
        
        e.preventDefault();

        $member = document.getElementById('memberSerailNo').value;
        $book = document.getElementById('bookSerailNo').value;

        console.log($member);
        console.log($book);
        
    });

});