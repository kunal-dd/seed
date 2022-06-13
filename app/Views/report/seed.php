
<?php echo $header; ?>
<?php echo $sidebar; ?>
<div class="content-page">
    <div class="content">
        <div class="">
            <div class="page-header-title">
                <h4 class="page-title">Seed Reports</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('Dashboard'); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Seed Reports</li>
                    </ol>
            </div>
        </div>
        <div class="page-content-wrapper ">
            <div class="container">
                <div class="row">
                    <div class="col-md-12" id="error_msg_info">
                        <?php if (session()->getFlashdata('success')) { ?>
                            <div class="alert alert-success fade in" style="margin-top:18px;">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                                <strong><?php echo session()->getFlashdata('success'); ?></strong> 
                            </div>
                        <?php } ?>
                        <?php if (session()->getFlashdata('error')) { ?>
                            <div class="alert alert-danger fade in" style="margin-top:18px;">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                                <strong><?php echo session()->getFlashdata('error'); ?></strong> 
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-primary">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label class="datefilterlabel"></label>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="startdate" placeholder="Start Date of seed">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" value="" id="enddate" class="form-control" placeholder="End Date of seed">
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered dataTable no-footer" id="transaction">
                                                <thead>
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Members No.</th>
                                                        <th>Date of seed</th>
                                                        <th>Amount planted this week</th>
                                                        <th>Added By</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                                <tfoot align="right">
                                                    <tr>
                                                        <th></th>
                                                        <th></th>
                                                        <th>Total :</th>
                                                        <th></th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php echo $footer; ?>
    <script type="text/javascript">
        $('#startdate').datepicker({
            format: "yyyy-mm-dd"
        });
        $('#enddate').datepicker({
            format: "yyyy-mm-dd"
        });
        function load_transactiontable() {
            var table;
            var table = jQuery('#transaction').DataTable({
                footerCallback: function (tfoot, data, start, end, display) {
                    var api = this.api();
                    var intVal = function (i) {
                        return typeof i === 'string' ?
                                i.replace(/[\$,]/g, '') * 1 :
                                typeof i === 'number' ?
                                i : 0;
                    };
                    $(api.column(3).footer()).html(
                            api.column(3).data().reduce(function (a, b) {
                        var total = intVal(a) + intVal(b);
                        return '$' + total;
                    }, 0)
                            );
                },
                "processing": true,
                "serverSide": true,
                "responsive": false,
                "pageLength": 1000,
                "order": [[0, "DESC"]],
                "ajax": {
                    url: "<?php echo site_url('Report/getseedtabledata'); ?>",
                    data: function (d) {
                        d.<?php echo csrf_token(); ?> = "<?php echo csrf_hash(); ?>";
                        d.from_date = function () {
                            return $('#startdate').val();
                        };
                        d.to_date = function () {
                            return $('#enddate').val();
                        };
                    },
                },

                "columns": [
                    {"taregts": 0, 'data': 'id', "visible": false,
                    },
                    {"taregts": 1, 'data': 'member_no',
                        "render": function (data, type, row) {
                            return row.name + '(' + data + ')';
                        }
                    },
                    {"taregts": 2, 'data': 'date_of_seed',
                        "searchable": false,
                        "render": function (data) {
                            var date = new Date(data);
                            var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                                "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                            var days = ['Sun', 'Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat'];
                            var mon_num = date.getMonth();
                            var day = date.getDay();
                            var month = monthNames[mon_num];
                            var hours = date.getHours();
                            var minutes = date.getMinutes();
                            var ampm = hours >= 12 ? 'PM' : 'AM';
                            hours = hours % 12;
                            hours = hours ? hours : 12; // the hour '0' should be '12'
                            minutes = minutes < 10 ? '0' + minutes : minutes;
                            if (data != null && data != '') {
                                //return (date.getFullYear()+"-"+ mon_num + "-" +date.getDate());
                                return date.getDate() + "-" + month + "-" + date.getFullYear();
                            } else {
                                return '';
                            }
                        }
                    },
                    {"taregts": 3, 'data': 'amount_planted_week',
                        "render": function (data, type, row) {
                            if (data != '') {
                                return '$' + data;
                            } else {
                                return '';
                            }
                        }
                    },
                    {"taregts": 4, 'data': 'firstname',
                        "render": function (data, type, row) {
                            return row.firstname + ' ' + row.lastname;
                        }
                    },
                ],

                dom: 'Bfrtip',
                buttons: [
                    {extend: 'excelHtml5', footer: true, text: 'Seed Report', filename: function () {
                            var d = new Date();
                            var date = d.getDate();
                            var month = d.getMonth() + 1;
                            var year = d.getFullYear();
                            return "seed-report" + date + "_" + month + "_" + year;
                        },
                        messageTop: function () {
                            var out = "";
                            out += 'Start Date : ' + $('#startdate').val() + '   ';
                            out += 'End Date : ' + $("#enddate").val();
                            return out;
                        },
                        title: function () {
                            return 'Seed Report'
                        },
                        customize: function (xlsx) {
                            $(xlsx.xl["styles.xml"]).find('numFmt[numFmtId="164"]').attr('formatCode', '[$$-45C] #,##0.00_-');
                        }
                    },
                ]
            }
            );

        }
        $(document).ready(function () {
            load_transactiontable();
            // reload_transaction_table();
        });

        $('#startdate').on('change', function () {
            reload_transaction_table();
        });
        $('#enddate').on('change', function () {
            reload_transaction_table();
        });
        function reload_transaction_table() {
            var oTable1 = $('#transaction').dataTable();
            oTable1.fnStandingRedraw();
        }



    </script>