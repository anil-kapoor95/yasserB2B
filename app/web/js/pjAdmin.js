var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();

(function ($) {
    "use strict";

    $(function () {
        const $datePickerOptions = $("#datePickerOptions");

        /* ================= DATEPICKER ================= */
        if ($datePickerOptions.length && $.fn.datetimepicker && typeof moment !== "undefined") {

            const weekStart = parseInt($datePickerOptions.data("wstart"), 10) || 0;
            const months    = ($datePickerOptions.data("months") || "").split("_");
            const days      = ($datePickerOptions.data("days") || "").split("_");
            const format    = $datePickerOptions.data("format") || "YYYY-MM-DD";

            moment.updateLocale("en", {
                week: { dow: weekStart },
                months: months,
                weekdaysMin: days
            });

            const dateOptions = {
                format: format,
                locale: moment.locale("en"),
                allowInputToggle: true,
                ignoreReadonly: true,
                useCurrent: false
            };

            $(".datetimepick_from").datetimepicker(dateOptions);
            $(".datetimepick_to").datetimepicker(dateOptions);

            $("#from_date").on("dp.change", function(e){
                $("#to_date").data("DateTimePicker")?.minDate(e.date);
            });

            $("#to_date").on("dp.change", function(e){
                $("#from_date").data("DateTimePicker")?.maxDate(e.date);
            });
        }

        /* ================= DASHBOARD CHARTS ================= */
        /* ================= B2B CHART ================= */
        
        if (window.dashboardData && typeof Chart !== "undefined") {
            const data = window.dashboardData;

            function createChart(id, config) {
                const canvas = document.getElementById(id);
                if (!canvas) return;
                new Chart(canvas, config);
            }

            /* Revenue Chart */
            createChart("revenueChart", {
                type: "line",
                data: {
                    labels: data.revenueTrend?.map(x => x.label) || [],
                    datasets: [{
                        label: "Revenue",
                        data: data.revenueTrend?.map(x => x.total) || [],
                        borderColor: "#4C786B",
                        backgroundColor: "rgba(26,179,148,0.1)",
                        tension: 0.4,
                        fill: true
                    }]
                },
                options:{
                    responsive:true,
                    maintainAspectRatio:false
                }
            });

            /* Bookings Chart */
            createChart("bookingChart", {
                type: "bar",
                data: {
                    labels: data.bookingsPerDay?.map(x => x.date) || [],
                    datasets: [{
                        label: "Bookings",
                        data: data.bookingsPerDay?.map(x => x.total) || [],
                        backgroundColor: "#569eac",
                        maxBarThickness: 10
                    }]
                },
                options:{
                    responsive:true,
                    maintainAspectRatio:false
                }
            });

            /* Status Chart */
            createChart("statusChart", {
                type: "pie",
                data: {
                    labels: data.statusChart?.map(x => `${x.status.charAt(0).toUpperCase() + x.status.slice(1)} [${x.total}]`) || [],
                    datasets: [{
                        data: data.statusChart?.map(x => x.total) || [],
                        backgroundColor: ["#4C786B", "#9BD0C0", "#569EAC", "#ed5565"]
                    }]
                },
                options:{
                    responsive:true,
                    maintainAspectRatio:false,
                    plugins:{ legend:{ position:"bottom", align: "start" } }
                }
            });

            /* Payment Chart */
            // createChart("paymentChart", {
            //     type: "doughnut",
            //     data: {
            //         labels: data.paymentChart?.map(x => `${x.payment_method} [${x.total}]`) || [],
            //         datasets: [{
            //             data: data.paymentChart?.map(x => x.total) || [],
            //             backgroundColor: ["#4C786B", "#9BD0C0", "#569EAC", "#ed5565"]
            //         }]
            //     },
            //     options:{
            //         responsive:true,
            //         maintainAspectRatio:false,
            //         cutout:"50%",
            //         plugins:{ legend:{ position:"bottom", align: "start" } }
            //     }
            // });

            createChart("paymentChart", {
                type: "doughnut",
                data: {
                    labels: data.paymentChart?.map(x => x.payment_method) || [],
                    datasets: [{
                        label: 'Bookings',
                        data: data.paymentChart?.map(x => x.total_count) || [], // counts
                        backgroundColor: ["#4C786B", "#9BD0C0", "#569EAC", "#ed5565"],
                        amounts: data.paymentChart?.map(x => parseFloat(x.total_amount)) || [] // numeric amounts
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: "50%",
                    plugins: {
                        legend: {
                            position: "bottom",
                            align: "start",
                            labels: {
                                generateLabels: function(chart) {
                                    const dataset = chart.data.datasets[0];
                                    return chart.data.labels.map((label, i) => {
                                        const amount = dataset.amounts[i];
                                        return {
                                            text: `${label} (${amount.toFixed(2)})`, // append amount here
                                            fillStyle: dataset.backgroundColor[i],
                                            strokeStyle: dataset.borderColor,
                                            lineWidth: dataset.borderWidth,
                                            hidden: !chart.isDatasetVisible(0),
                                            index: i
                                        };
                                    });
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const count = context.raw;
                                    const amount = context.dataset.amounts[context.dataIndex];
                                    return `${context.label}: Total = ${count}, Amount = ${amount.toFixed(2)}`;
                                }
                            }
                        }
                    }
                }
            });

            /* Revenue by Vehicle Chart */
            if (data.revenueByVehicleChart?.labels?.length > 0) {
                createChart("revenue_by_vehicle", {  // <-- use this ID
                    type: "doughnut",
                    data: {
                        labels: data.revenueByVehicleChart.labels,
                        datasets: [{
                            data: data.revenueByVehicleChart.data,
                            backgroundColor: ["#4C786B", "#9BD0C0", "#569EAC", "#ed5565"]
                        }]
                    },
                    options:{
                        responsive:true,
                        maintainAspectRatio:false,
                        cutout:"50%",
                        plugins:{ legend:{ position:"bottom", align: "start" } }
                    }
                });
            }
            
            createChart("peakBookingChart", {
                type: "bar",
                data: {
                    labels: data.peakBookingChart?.map(x => x.label) || [],
                    datasets: [{
                        label: "Peak Bookings",
                        data: data.peakBookingChart?.map(x => x.total) || [],
                        backgroundColor: "#1ab394",
                        maxBarThickness: 10
                    }]
                },
                options:{
                    responsive:true,
                    maintainAspectRatio:false
                }
            });
        }
        
        if (window.b2bChartData && typeof Chart !== "undefined") {

            function createChart(id, config) {
                const canvas = document.getElementById(id);
                if (!canvas) return;
                new Chart(canvas, config);
            }

            /* ================= B2B Ride Status (Donut) ================= */
            createChart("b2bChart", {
                type: "doughnut",
                data: {
                    labels: ["Available", "Upcoming", "Completed"],
                    datasets: [{
                        data: [
                            Number(window.b2bChartData.available || 0),
                            Number(window.b2bChartData.upcoming || 0),
                            Number(window.b2bChartData.completed || 0)
                        ],
                        backgroundColor: ["#4C786B", "#9BD0C0", "#569EAC"]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: "55%",
                    plugins: {
                        legend: {
                            position: "right"
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ": " + context.raw;
                                }
                            }
                        }
                    }
                }
            });

            /* ================= B2B Earnings (Bar Chart) ================= */

            const rawCommission = Number(window.b2bChartData.commission || 0);
            const rawPaid = Number(window.b2bChartData.paid || 0);

            // 👉 If both are zero → show "No Data"
            if (rawCommission === 0 && rawPaid === 0) {
                const container = document.getElementById("b2bRevenueChart");
                if (container) {
                    container.parentNode.innerHTML =
                        "<div style='text-align:center;padding:50px;color:#999;font-size:14px;'>No Earnings Data</div>";
                }
            } else {

                // 👉 Prevent invisible bars (Chart.js issue)
                const chartCommission = rawCommission === 0 ? 0.01 : rawCommission;
                const chartPaid = rawPaid === 0 ? 0.01 : rawPaid;

                createChart("b2bRevenueChart", {
                    type: "bar",
                    data: {
                        labels: ["Commission", "Paid"],
                        datasets: [{
                            data: [chartCommission, chartPaid],
                            backgroundColor: ["#569eac", "#4c786b"],
                            borderWidth: 0,
                            borderRadius: 6,
                            barThickness: 40
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,

                        scales: {
                            x: {
                                beginAtZero: true,
                                min: 0,
                                suggestedMax: Math.max(chartCommission, chartPaid) * 1.5,
                                grid: { display: false },
                                border: { display: false },
                                ticks: { display: false }
                            },
                            y: {
                                grid: { display: false },
                                border: { display: false },
                                ticks: { display: false }
                            }
                        },

                        plugins: {
                            legend: {
                                display: false
                            },

                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const values = [rawCommission, rawPaid];
                                        const realValue = values[context.dataIndex];
                                        return context.label + ": €" + realValue.toFixed(2);
                                    }
                                }
                            },

                            datalabels: {
                                color: function(context) {
                                    const values = [chartCommission, chartPaid];
                                    const value = values[context.dataIndex];
                                    const max = Math.max(...values);
                                    return value < max * 0.25 ? "#333" : "#fff";
                                },

                                anchor: function(context) {
                                    const values = [chartCommission, chartPaid];
                                    const value = values[context.dataIndex];
                                    const max = Math.max(...values);
                                    return value < max * 0.25 ? "end" : "center";
                                },

                                align: function(context) {
                                    const values = [chartCommission, chartPaid];
                                    const value = values[context.dataIndex];
                                    const max = Math.max(...values);
                                    return value < max * 0.25 ? "right" : "center";
                                },

                                offset: function(context) {
                                    const values = [chartCommission, chartPaid];
                                    const value = values[context.dataIndex];
                                    const max = Math.max(...values);
                                    return value < max * 0.25 ? 8 : 0;
                                },

                                formatter: function(value, context) {
                                    const labels = context.chart.data.labels;
                                    const values = [rawCommission, rawPaid];
                                    return `${labels[context.dataIndex]} €${values[context.dataIndex].toFixed(2)}`;
                                },

                                font: {
                                    weight: "bold",
                                    size: 12
                                },

                                clamp: true,
                                clip: false
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                });
            }
        }
        $('#bookingTabs button').on('click', function(){

            const type = $(this).data('type'); // date or hour
            const $form = $('.frm-filter');

            const params = new URLSearchParams({
                from_date: $form.find("input[name='from_date']").val(),
                to_date: $form.find("input[name='to_date']").val(),
                booking_status: $form.find("select[name='booking_status']").val(),
                payment_status: $form.find("select[name='payment_status']").val(),
                group: $form.find("input[name='group']").val(), // keep revenue tab
                analysis: type // <-- important
            });

            window.location.href = "index.php?controller=pjAdmin&action=pjActionIndex&" + params.toString();
        });

        $('#revenueTabs button').on('click', function(){
            const type = $(this).data('type'); // daily, weekly, monthly
            const $form = $('.frm-filter');

            // Get current filter values
            const params = new URLSearchParams({
                from_date: $form.find("input[name='from_date']").val(),
                to_date: $form.find("input[name='to_date']").val(),
                booking_status: $form.find("select[name='booking_status']").val(),
                payment_status: $form.find("select[name='payment_status']").val(),
                group: type
            });

            // Redirect with all filters + group type
            window.location.href = "index.php?controller=pjAdmin&action=pjActionIndex&" + params.toString();
        });
        /* ================= FILTER FORM ================= */
        $(document).on("submit", ".frm-filter", function(e) {
            e.preventDefault(); // prevent normal form submission

            const $form = $(this);
            const startDate = $form.find("input[name='from_date']").val();
            const endDate   = $form.find("input[name='to_date']").val();
            const bookingStatus = $form.find("select[name='booking_status']").val();
            const paymentStatus = $form.find("select[name='payment_status']").val();
            const timeType = $form.find("select[name='time_type']").val();
            const city = $form.find("select[name='city']").val();
            const fleet = $form.find("select[name='fleet_id']").val();

            // Build query string
            const params = new URLSearchParams({
                from_date: startDate,
                to_date: endDate,
                booking_status: bookingStatus,
                payment_status: paymentStatus,
                time_type: timeType,
                city: city,
                fleet_id: fleet,
            });

            // Redirect to URL with query params
            window.location.href = "index.php?controller=pjAdmin&action=pjActionIndex&" + params.toString();
        });

        // Optional: redraw charts when new dashboardData is received
        $(document).on("dashboardDataUpdated", function() {
            // remove old canvases and recreate
            ["revenueChart","bookingChart","statusChart","paymentChart"].forEach(function(id){
                const oldCanvas = document.getElementById(id);
                if (oldCanvas) oldCanvas.getContext("2d").clearRect(0,0,oldCanvas.width,oldCanvas.height);
            });

            if (window.dashboardData) {
                // Re-run the chart initialization
                // (reuse the same createChart code from above)
            }
        });

       $(document).ready(function () {

            $(document).on("click", "#btnExportBookings", function () {

                console.log("Export clicked"); // DEBUG

                let form = $("#exportForm");

                form.find("[name=from_date]").val($("#from_date").val());
                form.find("[name=to_date]").val($("#to_date").val());
                form.find("[name=booking_status]").val($("[name=booking_status]").val());
                form.find("[name=payment_status]").val($("[name=payment_status]").val());
                form.find("[name=time_type]").val($("[name=time_type]").val());
                form.find("[name=city]").val($("[name=city]").val());
                form.find("[name=fleet_id]").val($("[name=fleet_id]").val());

                console.log(form.serialize()); // DEBUG

                form.submit();
            });

        });
    });

})(jQuery_1_8_2);
