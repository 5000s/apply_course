<!-- Bootstrap core CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<link href="{{url("plugins/datepicker/css/datepicker.css")}}" rel="stylesheet" >
<link href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css" rel="stylesheet" >
<style>

    .table-light{
        font-weight: bold;
        text-align: center;
    }

    .req-in{
        color:red;
        font-weight: lighter;
        font-size: 25px;
    }

    .select-form {
        padding: 0.375rem 2.25rem 0.375rem 0.75rem;
        -moz-padding-start: calc(0.75rem - 3px);
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        min-width: 120px;
        color: #212529;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        appearance: none;
    }

    .select-form:disabled {
        background-color: #e9ecef;
    }

    .thing {
        margin-top: 30px;
        max-width: 100vw !important;
        width: 800px;

        padding: 1rem;
        box-shadow: 0 15px 30px 0 rgba(0, 0, 0, 0.11),
        0 5px 15px 0 rgba(0, 0, 0, 0.08);
        background-color: #ffffff;
        border-radius: 0.5rem;

        border-left: 0 solid #00ff99;
        transition: border-left 300ms ease-in-out, padding-left 300ms ease-in-out;
    }

    .thing:hover {
        padding-left: 0.5rem;
        border-left: 0.5rem solid #00ff99;
    }

    .thing > :first-child {
        margin-top: 0;
    }

    .thing > :last-child {
        margin-bottom: 0;
    }

</style>
