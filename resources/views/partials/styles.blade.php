{{-- Bootstrap 5 core CSS --}}
<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
    rel="stylesheet"
    crossorigin="anonymous"
/>

{{-- DataTables CSS --}}
<link
    href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css"
    rel="stylesheet"
/>

<style>
    /* make sure no parent container ever clips the dropdown */
    html, body, main.container-fluid, .container-fluid, .container {
        overflow: visible !important;
        position: relative !important;
        z-index: auto !important;
    }

    /* push the navbar above all page content */
    .navbar {
        position: relative;
        z-index: 2000;
    }

    /* ensure dropdown menus float on top */
    .dropdown-menu {
        position: absolute !important;
        z-index: 2050 !important;
    }

    /* your other utilities */
    .table-light {
        font-weight: bold;
        text-align: center;
    }
    .req-in {
        color: red;
        font-weight: lighter;
        font-size: 25px;
    }
    .select-form {
        padding: .375rem 2.25rem .375rem .75rem;
        font-size: 1rem;
        min-width: 120px;
        color: #212529;
        border: 1px solid #ced4da;
        border-radius: .25rem;
        transition: border-color .15s ease, box-shadow .15s ease;
        appearance: none;
    }
    .select-form:disabled {
        background-color: #e9ecef;
    }
    .thing {
        margin: 30px auto;
        max-width: 100vw !important;
        width: 800px;
        padding: 1rem;
        background: #fff;
        border-radius: .5rem;
        box-shadow:
            0 15px 30px rgba(0,0,0,0.11),
            0 5px 15px rgba(0,0,0,0.08);
        border-left: 0 solid #00ff99;
        transition: border-left .3s ease, padding-left .3s ease;
    }
    .thing:hover {
        padding-left: .5rem;
        border-left: .5rem solid #00ff99;
    }
    .thing > :first-child { margin-top: 0 }
    .thing > :last-child  { margin-bottom: 0 }
</style>
