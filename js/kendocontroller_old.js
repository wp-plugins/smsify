$ = jQuery;
	 $(document).ready(function() {
	 		/************** CHARTS **************/
	 			var activity_bymonth_ds = new kendo.data.DataSource({
					transport: {
				    read: {
				        	url: apiEndpoint + "/transport/?key=" + api_key + "&method=getMessagesByMonth",
				        	dataType: "json"
				       }
				   }
				});
				
				var activity_byday_ds = new kendo.data.DataSource({
					transport: {
				    read: {
				        	url: apiEndpoint + "/transport/?key=" + api_key + "&method=getMessagesByDay",
				        	dataType: "json"
				        }
				   }
				});
			
				function createMonthlyChart() {
				    $("#barchart").kendoChart({
				        theme: $(document).data("kendoSkin") || "default",
				        dataSource: activity_bymonth_ds,
				        title: {
				            text: "Monthly Activity Chart"
				        },
				        legend: {
				            position: "top"
				        },
				        seriesDefaults: {
				            type: "column"
				        },
				        series:
				        [{
			                field: "queued",
			                name: "Queued"
				        },
				        {
			                field: "inprogress",
			                name: "In Progress"
				        },{ 
			                field: "delivered",
			                name: "Delivered"
				        },{
			                field: "bounced",
			                name: "Bounced"
				        },{
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
				            visible: true,
				            template: "${category} - #= series.name #  (${value})"
				        }
				    });
				}
				
				function createDailyChart() {
				    $("#dailychart").kendoChart({
				        theme: $(document).data("kendoSkin") || "default",
				        dataSource: activity_byday_ds,
				        title: {
				            text: "24 Hour Activity Chart"
				        },
				        legend: {
				            position: "top"
				        },
				        seriesDefaults: {
				            type: "column"
				        },
				        series:
				        [{
			                field: "queued",
			                name: "Queued"
				        },
				        {
			                field: "inprogress",
			                name: "In Progress"
				        },{ 
			                field: "delivered",
			                name: "Delivered"
				        },{
			                field: "bounced",
			                name: "Bounced"
				        },{
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
				            visible: true,
				            template: "#= series.name #  (${value})"
				        }
				    });
				}
				setTimeout(function() {
			        // Initialize the chart with a delay to make sure
			        // the initial animation is visible
			        createMonthlyChart();
					createDailyChart();
					
			        $("#activitychart").bind("kendo:skinChange", function(e) {
			            createMonthlyChart();
			        });
			        $("#dailychart").bind("kendo:skinChange", function(e) {
			            createDailyChart();
			        });
			    }, 400);
    			/************** //CHARTS END **************/
    			
				var utcoffset = ((new Date()).getTimezoneOffset() / 60);

				hideContent();
				$('.content-item-0').show();

				var smsifymenu = $("#smsifymenu").kendoMenu({
					select: function(e) {
							// handle event
							hideContent();
							var currentContent = e.item.className.split(' ')[0];
							var selectedContentClass = ".content-" + currentContent;
							$(selectedContentClass).fadeIn();
							this.element.find(".k-state-selected").removeClass('k-state-selected');
							$(e.item).addClass('k-state-selected'); 
							 
							//MT: Dirty hack to redraw datagrid
							if(currentContent == "item-0") {
								ds_contacts.cancelChanges();  
							}
								
							if(currentContent == "item-10") {
								ds_groups_nodefault.cancelChanges();
							}

							if(currentContent == "item-20") {  
								$(".sendToGroupSuccess").hide();
							}
						}
				 });
				//highlight the first menu item on page load
				$('.item-0').addClass('k-state-selected');

				function hideContent() {
					$('.content-item-0').hide();
					$('.content-item-10').hide();
					$('.content-item-20').hide();
					$('.content-item-30').hide();
					$('.content-item-40').hide();
				}

				var ds_contacts = new kendo.data.DataSource({
						transport: {
								read: {
										url: apiEndpoint + "/transport/?key=" + api_key + "&method=getContacts&utcoffset="+utcoffset,
										dataType: "json"
								},
								update: {
										type: "post",
										url: apiEndpoint + "/transport/?updateContacts",
										dataType: "json",
										complete: refreshGroupsAndContacts2
								},
								create: {
										type: "post",
										url: apiEndpoint + "/transport/?createContacts",
										dataType: "json",
										complete: refreshGroupsAndContacts2
								},
								destroy: {
										type: "post",
										url: apiEndpoint + "/transport/?destroyContacts",
										dataType: "json",
										complete: refreshGroupsAndContacts2
								},
								parameterMap: function(options, operation) {
										if (operation !== "read" && options.models) {
												contactObj = {};
												switch(operation) {
														case "update" :
																contactObj = {models: kendo.stringify(options.models), method: "updateContacts", key: api_key}
																break;
														case "destroy" :
																contactObj = {models: kendo.stringify(options.models), method: "destroyContacts", key: api_key}
																break;
														case "create" :
																contactObj = {models: kendo.stringify(options.models), method: "createContacts", key: api_key}
																break;
														default :
																contactObj = {models: kendo.stringify(options.models)};
																break;
												}
												return contactObj;
										}
								}
						},
						batch: true,
						pageSize: 10,
						schema: {
								model: {
										id: "contact_id",
										fields: {
												contact_id: { editable: false, type: "number" },
												mobile_number: { validation: { required: true }, type: "string" },
												first_name: { type: "string" },
												last_name: { type: "string" },
												group_name: { validation: { required: true }, defaultValue: "Default" },
												dt_created: {editable: false }
										}
								}
						},
						error: function(e) {
							if(e.status == 500) {
								alert(e.responseText);
							}
						}
				});
				
				var smsiconTemplate = kendo.template('<a class="k-button quicksend" style="min-width:28px;" title="Quick send SMS to this number."><span class="k-icon k-i-maximize"></span></a>');
				
				var contactsgrid = $("#contactsgrid").kendoGrid({
							dataSource: ds_contacts,
							navigatable: false,
							pageable: true,
							sortable: true,
							groupable: true,
							filterable: true,
							height:445,
							toolbar: ["create", "save", "cancel"],
							columns:[
									{
											field: "mobile_number",
											title: "Mobile Number",
											width: 130
									},
									{
											field: "first_name",
											title: "First Name",
											width: 130
									},
									{
											field: "last_name",
											title: "Last Name",
											width: 130
									},
									{
											field: "group_name",
											title: "Group",
											width: 120,
											editor: groupDropDownEditor
									},
									{
											field: "dt_created",
											title: "Created",
											defaultValue: new Date(),
											width: 165
									},
									{	  command: {text: "Quicksend", template: smsiconTemplate},
											title: "SMS",
											width: 50,
											filterable: false
									},
									{ command: "destroy", title: " ", width: 105, filterable: false }],
							editable: true
					}).data("kendoGrid");
				
				wnd = $("#quicksendPopup").kendoWindow({
							 title: "Quicksend SMS",
							 modal: true,
							 visible: false,
							 resizable: false,
							 width: 320,
							 height: 540
			 }).data("kendoWindow");
				
								
				var quicksendTemplate = kendo.template($("#smsTemplate").html());

				$("#contactsgrid").delegate(".quicksend", "click", function(e) {
					e.preventDefault();
					var dataItem = contactsgrid.dataItem($(this).closest("tr"));
					wnd.content(quicksendTemplate(dataItem));
						wnd.center().open();
					var scheduleDatePicker = $("#scheduleDatePicker-quick").kendoDatePicker({format: "yyyy-MMM-dd"}).data("kendoDatePicker");
					var scheduleTimePicker = $("#scheduleTimePicker-quick").kendoTimePicker({format: "HH:mm:ss"}).data("kendoTimePicker");
					var today = new Date();
					
					scheduleDatePicker.min(today);
					
					 var viewModel = kendo.observable({
						scheduled: false
					})
					kendo.bind($("#schedule-container-quick"), viewModel);
						
						$(".btn_send_to_number").click(function(e) {
							e.preventDefault();
				var postData = {};
				postData.contact_id = $("#quicksend_contact_id").val();
				postData.message = $("#quicksendEditor").val();
				postData.method = "quicksendToMobile";
				postData.key = api_key;
				
				if(postData.message.length == 0) {
					alert("Message must not be empty");
				} else if($("#schedule-quick").is(':checked') && ($("#scheduleDatePicker-quick").val() == "" || $("#scheduleTimePicker-quick").val() == "")) {
							alert("When you tick the 'schedule' box, you must specify the date and time correctly. Untick the box if you don't wish to schedule this message");
				} else {
					if($("#schedule-quick").is(':checked')) {
								postData.schedule_date = $("#scheduleDatePicker-quick").val();
								postData.schedule_time = $("#scheduleTimePicker-quick").val();
								postData.timezone_offset = utcoffset;
							}
					$("#quicksendContainer").hide();
					$("#quicksendProgress").show();
					$.ajax({
						url: apiEndpoint + "/transport/",
						type: "POST",
						data: postData,
						success: quicksendSuccess,
						error: quicksendError
					})
				}
						});
				});   
				
				function quicksendSuccess(evt, request, settings) {
					$("#quicksendProgress").hide();
					var message = $.parseJSON(evt);
					if(message.status)	{
						$(".quicksendSuccess").show();
					} else {
					$("#quicksendContainer").show();
						alert(message.message);	   			
					}
					if(message.d) {
						smsifyUpdateCredits(message.d);
					}
					activity_bymonth_ds.read();
					activity_byday_ds.read();
				}
		
				function quicksendError(e) {
					alert("There was a problem sending your message.\n If problem persists, contact support@smsify.com.au");
				}

				var groups_transport = {
						read: {
								url: apiEndpoint + "/transport/?key=" + api_key + "&method=getGroups",
								dataType: "json"
						}
				};
					 
				var groups_nonempty_transport = {
						 read: {
								 url: apiEndpoint + "/transport/?key=" + api_key + "&method=getNonEmptyGroups",
								 dataType: "json",
								 complete: onNonEmptyGroupSuccess
						 }
				};
				
				function onNonEmptyGroupSuccess(evt, request, settings) {
					$("#smsify-loading").remove();
					$(".smsify-main-app").fadeIn();
					ds_contacts.cancelChanges(); //Hack to redraw the grid
				}
					 
			 	var groups_nodefault_transport = {
					read: {
							url: apiEndpoint + "/transport/?key=" + api_key + "&method=getGroupsNoDefault&utcoffset="+utcoffset,
							dataType: "json"
					},
					update: {
							type: "post",
							url: apiEndpoint + "/transport/?updateGroups",
							dataType: "json",
							complete: refreshGroups
					},
					create: {
							type: "post",
							url: apiEndpoint + "/transport/?createGroups",
							dataType: "json",
							complete: refreshGroups
					},
					destroy: {
							type: "post",
							url: apiEndpoint + "/transport/?destroyGroups",
							dataType: "json",
							complete: refreshGroupsAndContacts
					},
					parameterMap: function(options, operation) {
							if (operation !== "read" && options.models) {
									groupObj = {};
									switch(operation) {
											case "update" :
													groupObj = {models: kendo.stringify(options.models), method: "updateGroups", key: api_key}
													break;
											case "destroy" :
													groupObj = {models: kendo.stringify(options.models), method: "destroyGroups", key: api_key}
													break;
											case "create" :
													groupObj = {models: kendo.stringify(options.models), method: "createGroups", key: api_key}
													break;
											default :
													groupObj = {models: kendo.stringify(options.models)};
													break;
									}
									return groupObj;
							}
					}
			 };
					 
				var ds_groups = new kendo.data.DataSource({
						transport: groups_transport,
						batch: true,
						pageSize: 30,
						schema: {
								model: {
										id: "group_id",
										fields: {
												group_id: { editable: false, nullable: true },
												group_name: { validation: { required: true } }
										}
								}
						}
				});
				
				var ds_groups_nodefault = new kendo.data.DataSource({
						transport: groups_nodefault_transport,
						batch: true,
						pageSize: 30,
						schema: {
								model: {
										id: "group_id",
										fields: {
												group_id: { editable: false, nullable: true },
												group_name: { editable: true, nullable: false, validation: { required: true } },
												totalingroup: {editable: false, defaultValue: 0},
												dt_created: {editable: false}
										}
								}
						}
				});
				
				 var ds_groups_nonempty = new kendo.data.DataSource({
						transport: groups_nonempty_transport
				});
		
			function refreshGroups() {
				ds_groups_nodefault.read();
				ds_groups_nonempty.read();
				ds_groups.read();
			}
			
			function refreshGroupsAndContacts() {
				ds_groups_nodefault.read();
				ds_groups_nonempty.read();
				ds_groups.read();
				ds_contacts.read();
			}
			
			function refreshGroupsAndContacts2() {
				ds_groups_nodefault.read();
				ds_groups_nonempty.read();
				ds_contacts.read();
			}
				 
				$("#groupsgrid").kendoGrid({
					dataSource: ds_groups_nodefault,
					navigatable: false,
					pageable: true,
					sortable: true,
					height: 320,
					toolbar: ["create", "save", "cancel"],
					columns:[
							{
									field: "group_name",
									title: "Group Name"
							},
							{
									field: "totalingroup",
									title: "Total Contacts"
							},
							{
									field: "dt_created",
									title: "Date Created",
									defaultValue: new Date()
							},
							{ command: "destroy", title: " ", width: "110px" }],
					editable: true
				});
					
				$("#uploadGroup").kendoDropDownList({
					dataSource: ds_groups,
					dataTextField: "group_name",
					dataValueField: "group_id",
					index: 0
				});
				
				$("#selectSMSGroup").kendoDropDownList({
					dataSource: ds_groups_nonempty,
					dataTextField: "group_name",
					dataValueField: "group_id",
					index: 0
				});
				
				function groupDropDownEditor(container, options) {
					$('<input data-text-field="group_name" data-value-field="group_name" data-bind="value:' + options.field + '"/>')
					.appendTo(container)
					.kendoDropDownList({
						autoBind: true,
							dataSource: ds_groups,
							dataTextField: "group_name",
					dataValueField: "group_name"
					});
				}
				
				$("#csvfile").kendoUpload({
							async: {
									saveUrl: apiEndpoint + "/transport/",
									autoUpload: false
							},
							multiple: false,
							upload: onUpload,
							success: onCSVSuccess,
							error: onUploadError
					});
				
				$("#uploadform").kendoValidator();
				$("#sendSMStoGroup").kendoValidator({
					messages: {
						required: "This field is required"
					}	
				});
				
				$("#sendSMStoGroup").submit(function(e) {
					e.preventDefault();
					if($("#schedule").is(':checked') && ($("#scheduleDatePicker").val() == "" || $("#scheduleTimePicker").val() == "")) {
						alert("When you tick the 'schedule' box, you must specify the date and time correctly. Untick the box if you don't wish to schedule this message");
						return;
					}
					if(confirm("You are about to send a group message. Press OK to confirm.")) {	
						$("#btn_send_to_group").hide();
								$(".sendToGroupProgress").show();
								$(".sendToGroupSuccess").hide();
								var postData={};
								postData.method = "sendToGroup";
								postData.key = api_key;
								postData.group_id = $("#selectSMSGroup").val();
								postData.message = $("#smsEditor").val();
								if($("#schedule").is(':checked')) {
									postData.schedule_date = $("#scheduleDatePicker").val();
									postData.schedule_time = $("#scheduleTimePicker").val();
									postData.timezone_offset = utcoffset;
								}
								$.ajax({
							url: apiEndpoint + "/transport/",
							type: "POST",
							data: postData,
							success: sendToGroupSuccess,
							error: sendToGroupError
						})
					}
				});
				
				function sendToGroupSuccess(e) {
					$(".sendToGroupProgress").hide();
					$("#btn_send_to_group").show();
					var message = $.parseJSON(e);
					if(message.status)	{
						$(".sendToGroupSuccess").show();
					} else {
						alert(message.message);	   			
					}
					if(message.d) {	
						smsifyUpdateCredits(message.d);
					}
					activity_bymonth_ds.read();
					activity_byday_ds.read();
				}
				
				function sendToGroupError(e) {
					alert("There was a problem sending your message.\n If problem persists, contact support@smsify.com.au");
					//console.log("Error:: " + e);
				}
				
				function onUpload(e, request) {
					console.log(e);
					e.data = { method: "uploadContacts",
								key: api_key,
							   	group_id: $('#uploadGroup').val()
					};
				}
				
				function onUploadError(e) {
					e.preventDefault();
					alert(e.XMLHttpRequest.responseText);
				}
				
				function onCSVSuccess(e) {
						ds_contacts.read();
						ds_groups.read();
						ds_groups_nodefault.read();
						ds_groups_nonempty.read();
						if(e.response && e.response.hasOwnProperty('status') && e.response.status == 0)  {
							alert(e.response.message);
						} else {
							alert("Your contacts have been imported successfully!");
						}
				}
				
				var scheduleDatePicker = $("#scheduleDatePicker").kendoDatePicker({format: "yyyy-MMM-dd"}).data("kendoDatePicker"),
				scheduleTimePicker = $("#scheduleTimePicker").kendoTimePicker({format: "HH:mm:ss"}).data("kendoTimePicker"),
					
				today = new Date();
				//scheduleDatePicker.value(today);
				scheduleDatePicker.min(today);
				
				
				 var viewModel = kendo.observable({
					scheduled: false
				})
				kendo.bind($("#schedule-container"), viewModel);
		});
		
		function smsifyUpdateCredits(credits) {
			/*** Credit desplay ***/
			var content = 'Credits: ' + credits +  ', Buy more --&gt;';
			$(".smsify-credits").html(content);	
		}
		smsifyDisplayCredits(smsifyCredits);