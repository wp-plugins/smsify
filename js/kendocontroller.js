function smsifyUpdateCredits(e) {
    var t = "Credits: " + e + ", Buy more --&gt;";
    $(".smsify-credits").html(t)
}
$ = jQuery, $(document).ready(function () {
 	var version = "latest";
    function n() {
        $("#barchart").kendoChart({
            theme: $(document).data("kendoSkin") || "default",
            dataSource: e,
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
                field: "queued",
                name: "Queued"
            }, {
                field: "inprogress",
                name: "In Progress"
            }, {
                field: "delivered",
                name: "Delivered"
            }, {
                field: "bounced",
                name: "Bounced"
            }, {
                field: "scheduled",
                name: "Scheduled"
            }],
            seriesColors: ["#594ee6", "#ddce6b", "#337a1d", "#ff0060", "#000444"],
            categoryAxis: {
                field: "month",
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
    function r() {
        $("#dailychart").kendoChart({
            theme: $(document).data("kendoSkin") || "default",
            dataSource: t,
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
                field: "queued",
                name: "Queued"
            }, {
                field: "inprogress",
                name: "In Progress"
            }, {
                field: "delivered",
                name: "Delivered"
            }, {
                field: "bounced",
                name: "Bounced"
            }, {
                field: "scheduled",
                name: "Scheduled"
            }],
            seriesColors: ["#594ee6", "#ddce6b", "#337a1d", "#ff0060", "#000444"],
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
    function o() {
        $(".content-item-0").hide(), $(".content-item-10").hide(), $(".content-item-20").hide(), $(".content-item-30").hide(), $(".content-item-40").hide()
    }
    function c(n, r, i) {
        $("#quicksendProgress").hide();
        var s = $.parseJSON(n);
        s.status ? $(".quicksendSuccess").show() : ($("#quicksendContainer").show(), alert(s.message)), s.d && smsifyUpdateCredits(s.d), e.read(), t.read()
    }
    function h(e) {
        alert("There was a problem sending your message.\n If problem persists, contact support@smsify.com.au")
    }
    function v(e, t, n) {
        $("#smsify-loading").remove(), $(".smsify-main-app").fadeIn(), u.cancelChanges()
    }
    function w() {
        y.read(), b.read(), g.read()
    }
    function E() {
        y.read(), b.read(), g.read(), u.read()
    }
    function S() {
        y.read(), b.read(), u.read()
    }
    function x(e, t) {
        $('<input data-text-field="group_name" data-value-field="group_name" data-bind="value:' + t.field + '"/>').appendTo(e).kendoDropDownList({
            autoBind: !0,
            dataSource: g,
            dataTextField: "group_name",
            dataValueField: "group_name"
        })
    }
    function T(n) {
        $(".sendToGroupProgress").hide(), $("#btn_send_to_group").show();
        var r = $.parseJSON(n);
        r.status ? $(".sendToGroupSuccess").show() : alert(r.message), r.d && smsifyUpdateCredits(r.d), e.read(), t.read()
    }
    function N(e) {
        alert("There was a problem sending your message.\n If problem persists, contact support@smsify.com.au")
    }
    function C(e, t) {
        console.log(e), e.data = {
            method: "uploadContacts",
            key: api_key,
            version: version,
            group_id: $("#uploadGroup").val()
        }
    }
    function k(e) {
        e.preventDefault(), alert(e.XMLHttpRequest.responseText)
    }
    function L(e) {
        u.read(), g.read(), y.read(), b.read(), e.response && e.response.hasOwnProperty("status") && e.response.status == 0 ? alert(e.response.message) : alert("Your contacts have been imported successfully!")
    }
    var e = new kendo.data.DataSource({
        transport: {
            read: {
                url: apiEndpoint + "/transport/?key=" + api_key + "&version=" + version + "&method=getMessagesByMonth",
                dataType: "json"
            }
        }
    }),
        t = new kendo.data.DataSource({
            transport: {
                read: {
                    url: apiEndpoint + "/transport/?key=" + api_key + "&version=" + version + "&method=getMessagesByDay",
                    dataType: "json"
                }
            }
        });
    setTimeout(function () {
        n(), r(), $("#activitychart").bind("kendo:skinChange", function (e) {
            n()
        }), $("#dailychart").bind("kendo:skinChange", function (e) {
            r()
        })
    }, 400);
    var i = (new Date).getTimezoneOffset() / 60;
    o(), $(".content-item-0").show();
    var s = $("#smsifymenu").kendoMenu({
        select: function (e) {
            o();
            var t = e.item.className.split(" ")[0],
                n = ".content-" + t;
            $(n).fadeIn(), this.element.find(".k-state-selected").removeClass("k-state-selected"), $(e.item).addClass("k-state-selected"), t == "item-0" && u.cancelChanges(), t == "item-10" && y.cancelChanges(), t == "item-20" && $(".sendToGroupSuccess").hide()
        }
    });
    $(".item-0").addClass("k-state-selected");
    var u = new kendo.data.DataSource({
        transport: {
            read: {
                url: apiEndpoint + "/transport/?key=" + api_key + "&version=" + version + "&method=getContacts&utcoffset=" + i,
                dataType: "json"
            },
            update: {
                type: "post",
                url: apiEndpoint + "/transport/?updateContacts",
                dataType: "json",
                complete: S
            },
            create: {
                type: "post",
                url: apiEndpoint + "/transport/?createContacts",
                dataType: "json",
                complete: S
            },
            destroy: {
                type: "post",
                url: apiEndpoint + "/transport/?destroyContacts",
                dataType: "json",
                complete: S
            },
            parameterMap: function (e, t) {
                if (t !== "read" && e.models) {
                    contactObj = {};
                    switch (t) {
                        case "update":
                            contactObj = {
                                models: kendo.stringify(e.models),
                                method: "updateContacts",
                                key: api_key,
                                version: version
                            };
                            break;
                        case "destroy":
                            contactObj = {
                                models: kendo.stringify(e.models),
                                method: "destroyContacts",
                                key: api_key,
                                version: version
                            };
                            break;
                        case "create":
                            contactObj = {
                                models: kendo.stringify(e.models),
                                method: "createContacts",
                                key: api_key,
                                version: version
                            };
                            break;
                        default:
                            contactObj = {
                                models: kendo.stringify(e.models)
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
                    }
                }
            }
        },
        error: function (e) {
            e.status == 500 && alert(e.responseText)
        }
    }),
        a = kendo.template('<a class="k-button quicksend" style="min-width:28px;" title="Quick send SMS to this number."><span class="k-icon k-i-maximize"></span></a>'),
        f = $("#contactsgrid").kendoGrid({
            dataSource: u,
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
                editor: x
            }, {
                field: "dt_created",
                title: "Created",
                defaultValue: new Date,
                width: 165
            }, {
                command: {
                    text: "Quicksend",
                    template: a
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
    var l = kendo.template($("#smsTemplate").html());
    $("#contactsgrid").delegate(".quicksend", "click", function (e) {
        e.preventDefault();
        var t = f.dataItem($(this).closest("tr"));
        wnd.content(l(t)), wnd.center().open();
        var n = $("#scheduleDatePicker-quick").kendoDatePicker({
            format: "yyyy-MMM-dd"
        }).data("kendoDatePicker"),
            r = $("#scheduleTimePicker-quick").kendoTimePicker({
                format: "HH:mm:ss"
            }).data("kendoTimePicker"),
            s = new Date;
        n.min(s);
        $("#run_every-quick").kendoComboBox();
        $("#run_times-quick").kendoNumericTextBox();
        var o = kendo.observable({
            scheduled: !1
        });
        kendo.bind($("#schedule-container-quick"), o), $(".btn_send_to_number").click(function (e) {
            e.preventDefault();
            var t = {};
            t.contact_id = $("#quicksend_contact_id").val(), t.message = $("#quicksendEditor").val(), t.method = "quicksendToMobile", t.key = api_key, t.version = version, t.message.length == 0 ? alert("Message must not be empty") : !$("#schedule-quick").is(":checked") || $("#scheduleDatePicker-quick").val() != "" && $("#scheduleTimePicker-quick").val() != "" ? ($("#schedule-quick").is(":checked") && (t.schedule_date = $("#scheduleDatePicker-quick").val(), t.schedule_time = $("#scheduleTimePicker-quick").val(), t.timezone_offset = i, t.run_every = $("#run_every-quick").val(), t.run_times = $("#run_times-quick").val()), $("#quicksendContainer").hide(), $("#quicksendProgress").show(), $.ajax({
                url: apiEndpoint + "/transport/",
                type: "POST",
                data: t,
                success: c,
                error: h
            })) : alert("When you tick the 'schedule' box, you must specify the date and time correctly. Untick the box if you don't wish to schedule this message")
        })
    });
    var p = {
        read: {
            url: apiEndpoint + "/transport/?key=" + api_key + "&version=" + version + "&method=getGroups",
            dataType: "json"
        }
    }, d = {
        read: {
            url: apiEndpoint + "/transport/?key=" + api_key + "&version=" + version + "&method=getNonEmptyGroups",
            dataType: "json",
            complete: v
        }
    }, m = {
        read: {
            url: apiEndpoint + "/transport/?key=" + api_key + "&version=" + version + "&method=getGroupsNoDefault&utcoffset=" + i,
            dataType: "json"
        },
        update: {
            type: "post",
            url: apiEndpoint + "/transport/?updateGroups",
            dataType: "json",
            complete: w
        },
        create: {
            type: "post",
            url: apiEndpoint + "/transport/?createGroups",
            dataType: "json",
            complete: w
        },
        destroy: {
            type: "post",
            url: apiEndpoint + "/transport/?destroyGroups",
            dataType: "json",
            complete: E
        },
        parameterMap: function (e, t) {
            if (t !== "read" && e.models) {
                groupObj = {};
                switch (t) {
                    case "update":
                        groupObj = {
                            models: kendo.stringify(e.models),
                            method: "updateGroups",
                            key: api_key,
                            version: version
                        };
                        break;
                    case "destroy":
                        groupObj = {
                            models: kendo.stringify(e.models),
                            method: "destroyGroups",
                            key: api_key,
                            version: version
                        };
                        break;
                    case "create":
                        groupObj = {
                            models: kendo.stringify(e.models),
                            method: "createGroups",
                            key: api_key,
                            version: version
                        };
                        break;
                    default:
                        groupObj = {
                            models: kendo.stringify(e.models)
                        }
                }
                return groupObj
            }
        }
    }, g = new kendo.data.DataSource({
        transport: p,
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
        y = new kendo.data.DataSource({
            transport: m,
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
        b = new kendo.data.DataSource({
            transport: d
        });
    $("#groupsgrid").kendoGrid({
        dataSource: y,
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
        dataSource: g,
        dataTextField: "group_name",
        dataValueField: "group_id",
        index: 0
    }), $("#selectSMSGroup").kendoDropDownList({
        dataSource: b,
        dataTextField: "group_name",
        dataValueField: "group_id",
        index: 0
    }), $("#csvfile").kendoUpload({
        async: {
            saveUrl: apiEndpoint + "/transport/",
            autoUpload: !1
        },
        multiple: !1,
        upload: C,
        success: L,
        error: k
    }), $("#uploadform").kendoValidator(), $("#sendSMStoGroup").kendoValidator({
        messages: {
            required: "This field is required"
        }
    }), $("#sendSMStoGroup").submit(function (e) {
        e.preventDefault();
        if (!(!$("#schedule").is(":checked") || $("#scheduleDatePicker").val() != "" && $("#scheduleTimePicker").val() != "")) {
            alert("When you tick the 'schedule' box, you must specify the date and time correctly. Untick the box if you don't wish to schedule this message");
            return
        }
        if (confirm("You are about to send a group message. Press OK to confirm.")) {
            $("#btn_send_to_group").hide(), $(".sendToGroupProgress").show(), $(".sendToGroupSuccess").hide();
            var t = {};
            t.method = "sendToGroup", t.key = api_key, t.version = version, t.group_id = $("#selectSMSGroup").val(), t.message = $("#smsEditor").val(), $("#schedule").is(":checked") && (t.schedule_date = $("#scheduleDatePicker").val(), t.schedule_time = $("#scheduleTimePicker").val(), t.run_every = $("#run_every").val(), t.run_times = $("#run_times").val(), t.timezone_offset = i), $.ajax({
                url: apiEndpoint + "/transport/",
                type: "POST",
                data: t,
                success: T,
                error: N
            })
        }
    });
    var A = $("#scheduleDatePicker").kendoDatePicker({
        format: "yyyy-MMM-dd"
    }).data("kendoDatePicker"),
        O = $("#scheduleTimePicker").kendoTimePicker({
            format: "HH:mm:ss"
        }).data("kendoTimePicker"),
        M = new Date;
    A.min(M);
    $("#run_every").kendoComboBox();
    $("#run_times").kendoNumericTextBox();
    var _ = kendo.observable({
        scheduled: !1
    });
    kendo.bind($("#schedule-container"), _)
}), smsifyDisplayCredits(smsifyCredits);