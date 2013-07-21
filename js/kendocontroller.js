function smsifyUpdateCredits(e) {
    var content = "Credits: " + e + ", Buy more --&gt;";
    $(".smsify-credits").html(content)
}
$ = jQuery, $(document).ready(function () {
    function t() {
        $("#barchart").kendoChart({
            theme: $(document).data("kendoSkin") || "default",
            dataSource: m,
            title: {
                text: "Monthly Activity Chart"
            },
            legend: {
                position: "top"
            },
            seriesDefaults: {
                type: "column"
            },
            series: [{
                field: "delivered",
                name: "Delivered"
            }, {
                field: "bounced",
                name: "Bounced"
            }],
            seriesColors: ["#594ee6", "#ff0000"],
            categoryAxis: {
                field: "monthname",
                labels: {
                    rotation: -90
                }
            },
            valueAxis: {
                labels: {
                    format: "{0:N0}"
                }
            },
            tooltip: {
                visible: !0,
                template: "${category} - #= series.name #  (${value})"
            }
        })
    }

    function n() {
        $("#dailychart").kendoChart({
            theme: $(document).data("kendoSkin") || "default",
            dataSource: g,
            title: {
                text: "24 Hour Activity Chart"
            },
            legend: {
                position: "top"
            },
            seriesDefaults: {
                type: "column"
            },
            series: [{
                field: "delivered",
                name: "Delivered"
            }, {
                field: "bounced",
                name: "Bounced"
            }],
            seriesColors: ["#594ee6", "#ff0000"],
            valueAxis: {
                labels: {
                    format: "{0:N0}"
                }
            },
            tooltip: {
                visible: !0,
                template: "#= series.name #  (${value})"
            }
        })
    }

    function r() {
        $(".content-item-0").hide(), $(".content-item-10").hide(), $(".content-item-20").hide(), $(".content-item-30").hide(), $(".content-item-40").hide(), $(".content-item-50").hide()
    }

    function i(e, t, n) {
        $("#quicksendProgress").hide();
        var r = $.parseJSON(e);
        r.status ? $(".quicksendSuccess").show() : ($("#quicksendContainer").show(), alert(r.message)), r.d && smsifyUpdateCredits(r.d), m.read(), g.read(), w.read()
    }

    function s(e) {
        alert("There was a problem sending your message.\n If problem persists, contact support@smsify.com.au")
    }

    function o(e, t, n) {
        $("#smsify-loading").remove(), $(".smsify-main-app").fadeIn(), x.cancelChanges()
    }

    function u() {
        M.read(), _.read(), O.read()
    }

    function a() {
        M.read(), _.read(), O.read(), x.read()
    }

    function f() {
        M.read(), _.read(), x.read()
    }

    function l(e, t) {
        $('<input data-text-field="group_name" data-value-field="group_name" data-bind="value:' + t.field + '"/>').appendTo(e).kendoDropDownList({
            autoBind: !0,
            dataSource: O,
            dataTextField: "group_name",
            dataValueField: "group_name"
        })
    }

    function onGroupSuccess(e) {
        $(".sendToGroupProgress").hide(), $("#btn_send_to_group").show();
        var t = $.parseJSON(e);
        t.status ? $(".sendToGroupSuccess").show() : alert(t.message), t.d && smsifyUpdateCredits(t.d), m.read(), g.read(), w.read()
    }

    function h(e) {
        alert("There was a problem sending your message.\n If problem persists, contact support@smsify.com.au")
    }

    function p(t, n) {
        console.log(t), t.data = {
            method: "uploadContacts",
            key: api_key,
            version: e,
            group_id: $("#uploadGroup").val()
        }
    }

    function d(e) {
        e.preventDefault(), alert(e.XMLHttpRequest.responseText)
    }

    function v(e) {
        x.read(), O.read(), M.read(), _.read(), e.response && e.response.hasOwnProperty("status") && e.response.status == 0 ? alert(e.response.message) : alert("Your contacts have been imported successfully!")
    }

    function S(e) {
        var t = $.parseJSON(e.responseText);
        t.s ? alert("Your task(s) have been cancelled successfully!") : alert(t.m), w.read()
    }
    var e = "latest",
        m = new kendo.data.DataSource({
            transport: {
                read: {
                    url: apiEndpoint + "/transport/?key=" + api_key + "&version=" + e + "&method=getMessagesByMonth",
                    dataType: "json"
                }
            }
        }),
        g = new kendo.data.DataSource({
            transport: {
                read: {
                    url: apiEndpoint + "/transport/?key=" + api_key + "&version=" + e + "&method=getMessagesByDay",
                    dataType: "json"
                }
            }
        });
    setTimeout(function () {
        t(), n(), $("#activitychart").bind("kendo:skinChange", function (e) {
            t()
        }), $("#dailychart").bind("kendo:skinChange", function (e) {
            n()
        })
    }, 400);
    var y = (new Date).getTimezoneOffset() / 60;
    r(), $(".content-item-0").show();
    var b = $("#smsifymenu").kendoMenu({
        select: function (e) {
            r();
            var t = e.item.className.split(" ")[0],
                n = ".content-" + t;
            $(n).fadeIn(), this.element.find(".k-state-selected").removeClass("k-state-selected"), $(e.item).addClass("k-state-selected"), t == "item-0" && x.cancelChanges(), t == "item-10" && M.cancelChanges(), t == "item-20" && $(".sendToGroupSuccess").hide(), t == "item-50" && w.cancelChanges()
        }
    });
    $(".item-0").addClass("k-state-selected");
    var w = new kendo.data.DataSource({
        transport: {
            read: {
                url: apiEndpoint + "/transport/?key=" + api_key + "&method=getSchedules&utcoffset=" + y + "&version=" + e,
                dataType: "json"
            },
            destroy: {
                type: "post",
                url: apiEndpoint + "/transport/?destroySchedules",
                dataType: "json",
                complete: S
            },
            parameterMap: function (t, n) {
                if (n !== "read" && t.models) return contactObj = {}, contactObj = {
                    models: kendo.stringify(t.models),
                    method: "destroySchedules",
                    key: api_key,
                    version: e
                }, contactObj
            }
        },
        batch: !0,
        pageSize: 10,
        schema: {
            model: {
                id: "remote_task_id",
                fields: {
                    remote_task_id: {
                        editable: !1,
                        type: "string"
                    },
                    dt_created: {
                        editable: !1
                    },
                    start_at: {
                        editable: !1
                    },
                    dt_last_run: {
                        editable: !1
                    },
                    run_times: {
                        editable: !1,
                        type: "number"
                    },
                    actual_run_times: {
                        editable: !1,
                        type: "number"
                    },
                    run_every: {
                        editable: !1,
                        type: "string"
                    }
                }
            }
        },
        error: function (e) {
            e.status == 500 && alert(e.responseText)
        }
    }),
        E = $("#schedulesgrid").kendoGrid({
            dataSource: w,
            navigatable: !1,
            pageable: !0,
            sortable: !0,
            groupable: !1,
            filterable: !0,
            height: 445,
            toolbar: ["save", "cancel"],
            columns: [{
                field: "dt_created",
                title: "Created",
                template: "#= kendo.toString(dt_created) #",
                width: 140
            }, {
                field: "start_at",
                title: "Start At",
                template: "#= kendo.toString(start_at) #",
                width: 140
            }, {
                field: "dt_last_run",
                title: "Last Run",
                template: "#= kendo.toString(dt_last_run) #",
                width: 140
            }, {
                field: "run_times",
                title: "Run Times",
                width: 75
            }, {
                field: "actual_run_times",
                title: "Run Count",
                width: 75
            }, {
                field: "run_every",
                title: "Run Every (Days)",
                width: 110
            }, {
                command: "destroy",
                title: " ",
                width: 80,
                filterable: !1
            }],
            editable: !0
        }).data("kendoGrid"),
        x = new kendo.data.DataSource({
            transport: {
                read: {
                    url: apiEndpoint + "/transport/?key=" + api_key + "&version=" + e + "&method=getContacts&utcoffset=" + y,
                    dataType: "json"
                },
                update: {
                    type: "post",
                    url: apiEndpoint + "/transport/?updateContacts",
                    dataType: "json",
                    complete: f
                },
                create: {
                    type: "post",
                    url: apiEndpoint + "/transport/?createContacts",
                    dataType: "json",
                    complete: f
                },
                destroy: {
                    type: "post",
                    url: apiEndpoint + "/transport/?destroyContacts",
                    dataType: "json",
                    complete: f
                },
                parameterMap: function (t, n) {
                    if (n !== "read" && t.models) {
                        contactObj = {};
                        switch (n) {
                        case "update":
                            contactObj = {
                                models: kendo.stringify(t.models),
                                method: "updateContacts",
                                key: api_key,
                                version: e
                            };
                            break;
                        case "destroy":
                            contactObj = {
                                models: kendo.stringify(t.models),
                                method: "destroyContacts",
                                key: api_key,
                                version: e
                            };
                            break;
                        case "create":
                            contactObj = {
                                models: kendo.stringify(t.models),
                                method: "createContacts",
                                key: api_key,
                                version: e
                            };
                            break;
                        default:
                            contactObj = {
                                models: kendo.stringify(t.models)
                            }
                        }
                        return contactObj
                    }
                }
            },
            batch: !0,
            pageSize: 10,
            schema: {
                model: {
                    id: "contact_id",
                    fields: {
                        contact_id: {
                            editable: !1,
                            type: "number"
                        },
                        mobile_number: {
                            validation: {
                                required: !0
                            },
                            type: "string"
                        },
                        first_name: {
                            type: "string"
                        },
                        last_name: {
                            type: "string"
                        },
                        group_name: {
                            validation: {
                                required: !0
                            },
                            defaultValue: "Default"
                        },
                        dt_created: {
                            editable: !1
                        },
                        email: {
                            type: "email"
                        }
                    }
                }
            },
            error: function (e) {
                e.status == 500 && alert(e.responseText)
            }
        }),
        T = kendo.template('<a class="k-button quicksend" style="min-width:28px;" title="Quick send SMS to this number."><span class="k-icon k-i-maximize"></span></a>'),
        N = $("#contactsgrid").kendoGrid({
            dataSource: x,
            navigatable: !1,
            pageable: !0,
            sortable: !0,
            groupable: !0,
            filterable: !0,
            height: 445,
            toolbar: ["create", "save", "cancel"],
            columns: [{
                field: "mobile_number",
                title: "Mobile Number",
                width: 130
            }, {
                field: "first_name",
                title: "First Name",
                width: 130
            }, {
                field: "last_name",
                title: "Last Name",
                width: 130
            }, {
                field: "group_name",
                title: "Group",
                width: 120,
                editor: l
            }, {
                field: "email",
                title: "Email",
                width: 165
            }, {
                command: {
                    text: "Quicksend",
                    template: T
                },
                title: "SMS",
                width: 50,
                filterable: !1
            }, {
                command: "destroy",
                title: " ",
                width: 105,
                filterable: !1
            }],
            editable: !0
        }).data("kendoGrid");
    wnd = $("#quicksendPopup").kendoWindow({
        title: "Quicksend SMS",
        modal: !0,
        visible: !1,
        resizable: !1,
        width: 320,
        height: 540
    }).data("kendoWindow");
    var C = kendo.template($("#smsTemplate").html());
    $("#contactsgrid").delegate(".quicksend", "click", function (t) {
        t.preventDefault();
        var n = N.dataItem($(this).closest("tr"));
        wnd.content(C(n)), wnd.center().open();
        var r = $("#scheduleDatePicker-quick").kendoDatePicker({
            format: "yyyy-MMM-dd"
        }).data("kendoDatePicker"),
            o = $("#scheduleTimePicker-quick").kendoTimePicker({
                format: "HH:mm:ss"
            }).data("kendoTimePicker"),
            u = new Date;
        r.min(u), $("#run_every-quick").kendoComboBox(), $("#run_times-quick").kendoNumericTextBox();
        var a = kendo.observable({
            scheduled: !1
        });
        kendo.bind($("#schedule-container-quick"), a), $(".btn_send_to_number").click(function (t) {
            t.preventDefault();
            var n = {};
            n.contact_id = $("#quicksend_contact_id").val(), n.message = $("#quicksendEditor").val(), n.method = "quicksendToMobile", n.key = api_key, n.version = e, n.message.length == 0 ? alert("Message must not be empty") : !$("#schedule-quick").is(":checked") || $("#scheduleDatePicker-quick").val() != "" && $("#scheduleTimePicker-quick").val() != "" ? ($("#schedule-quick").is(":checked") && (n.schedule_date = $("#scheduleDatePicker-quick").val(), n.schedule_time = $("#scheduleTimePicker-quick").val(), n.timezone_offset = y, n.run_every = $("#run_every-quick").val(), n.run_times = $("#run_times-quick").val()), $("#quicksendContainer").hide(), $("#quicksendProgress").show(), $.ajax({
                url: apiEndpoint + "/transport/",
                type: "POST",
                data: n,
                success: i,
                error: s
            })) : alert("When you tick the 'schedule' box, you must specify the date and time correctly. Untick the box if you don't wish to schedule this message")
        })
    });
    var k = {
        read: {
            url: apiEndpoint + "/transport/?key=" + api_key + "&version=" + e + "&method=getGroups",
            dataType: "json"
        }
    }, L = {
            read: {
                url: apiEndpoint + "/transport/?key=" + api_key + "&version=" + e + "&method=getNonEmptyGroups",
                dataType: "json",
                complete: o
            }
        }, A = {
            read: {
                url: apiEndpoint + "/transport/?key=" + api_key + "&version=" + e + "&method=getGroupsNoDefault&utcoffset=" + y,
                dataType: "json"
            },
            update: {
                type: "post",
                url: apiEndpoint + "/transport/?updateGroups",
                dataType: "json",
                complete: u
            },
            create: {
                type: "post",
                url: apiEndpoint + "/transport/?createGroups",
                dataType: "json",
                complete: u
            },
            destroy: {
                type: "post",
                url: apiEndpoint + "/transport/?destroyGroups",
                dataType: "json",
                complete: a
            },
            parameterMap: function (t, n) {
                if (n !== "read" && t.models) {
                    groupObj = {};
                    switch (n) {
                    case "update":
                        groupObj = {
                            models: kendo.stringify(t.models),
                            method: "updateGroups",
                            key: api_key,
                            version: e
                        };
                        break;
                    case "destroy":
                        groupObj = {
                            models: kendo.stringify(t.models),
                            method: "destroyGroups",
                            key: api_key,
                            version: e
                        };
                        break;
                    case "create":
                        groupObj = {
                            models: kendo.stringify(t.models),
                            method: "createGroups",
                            key: api_key,
                            version: e
                        };
                        break;
                    default:
                        groupObj = {
                            models: kendo.stringify(t.models)
                        }
                    }
                    return groupObj
                }
            }
        }, O = new kendo.data.DataSource({
            transport: k,
            batch: !0,
            pageSize: 30,
            schema: {
                model: {
                    id: "group_id",
                    fields: {
                        group_id: {
                            editable: !1,
                            nullable: !0
                        },
                        group_name: {
                            validation: {
                                required: !0
                            }
                        }
                    }
                }
            }
        }),
        M = new kendo.data.DataSource({
            transport: A,
            batch: !0,
            pageSize: 30,
            schema: {
                model: {
                    id: "group_id",
                    fields: {
                        group_id: {
                            editable: !1,
                            nullable: !0
                        },
                        group_name: {
                            editable: !0,
                            nullable: !1,
                            validation: {
                                required: !0
                            }
                        },
                        totalingroup: {
                            editable: !1,
                            defaultValue: 0
                        },
                        dt_created: {
                            editable: !1
                        }
                    }
                }
            }
        }),
        _ = new kendo.data.DataSource({
            transport: L
        });
    $("#groupsgrid").kendoGrid({
        dataSource: M,
        navigatable: !1,
        pageable: !0,
        sortable: !0,
        height: 320,
        toolbar: ["create", "save", "cancel"],
        columns: [{
            field: "group_name",
            title: "Group Name"
        }, {
            field: "totalingroup",
            title: "Total Contacts"
        }, {
            field: "dt_created",
            title: "Date Created",
            defaultValue: new Date
        }, {
            command: "destroy",
            title: " ",
            width: "110px"
        }],
        editable: !0
    }), $("#uploadGroup").kendoDropDownList({
        dataSource: O,
        dataTextField: "group_name",
        dataValueField: "group_id",
        index: 0
    }), $("#selectSMSGroup").kendoDropDownList({
        dataSource: _,
        dataTextField: "group_name",
        dataValueField: "group_id",
        index: 0
    }), $("#csvfile").kendoUpload({
        async: {
            saveUrl: apiEndpoint + "/transport/",
            autoUpload: !1
        },
        multiple: !1,
        upload: p,
        success: v,
        error: d
    }), $("#uploadform").kendoValidator(), $("#sendSMStoGroup").kendoValidator({
        messages: {
            required: "This field is required"
        }
    }), $("#sendSMStoGroup").submit(function (t) {
        t.preventDefault();
        if (!(!$("#schedule").is(":checked") || $("#scheduleDatePicker").val() != "" && $("#scheduleTimePicker").val() != "")) {
            alert("When you tick the 'schedule' box, you must specify the date and time correctly. Untick the box if you don't wish to schedule this message");
            return
        }
        if (confirm("You are about to send a group message. Press OK to confirm.")) {
            $("#btn_send_to_group").hide(), $(".sendToGroupProgress").show(), $(".sendToGroupSuccess").hide();
            var n = {};
            n.method = "sendToGroup", n.key = api_key, n.version = e, n.group_id = $("#selectSMSGroup").val(), n.message = $("#smsEditor").val(), $("#schedule").is(":checked") && (n.schedule_date = $("#scheduleDatePicker").val(), n.schedule_time = $("#scheduleTimePicker").val(), n.run_every = $("#run_every").val(), n.run_times = $("#run_times").val(), n.timezone_offset = y), $.ajax({
                url: apiEndpoint + "/transport/",
                type: "POST",
                data: n,
                success: onGroupSuccess,
                error: h
            })
        }
    });
    var D = $("#scheduleDatePicker").kendoDatePicker({
        format: "yyyy-MMM-dd"
    }).data("kendoDatePicker"),
        P = $("#scheduleTimePicker").kendoTimePicker({
            format: "HH:mm:ss"
        }).data("kendoTimePicker"),
        H = new Date;
    D.min(H), $("#run_every").kendoComboBox(), $("#run_times").kendoNumericTextBox();
    var B = kendo.observable({
        scheduled: !1
    });
    kendo.bind($("#schedule-container"), B)
}), smsifyDisplayCredits(smsifyCredits);