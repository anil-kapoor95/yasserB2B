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
                        maxBarThickness: 40
                    }]
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
                options: {
                    responsive: true,
                    plugins: { legend: { position: "bottom", align: "start" } }
                }
            });

            /* Payment Chart */
            createChart("paymentChart", {
                type: "doughnut",
                data: {
                    labels: data.paymentChart?.map(x => `${x.payment_method} [${x.total}]`) || [],
                    datasets: [{
                        data: data.paymentChart?.map(x => x.total) || [],
                        backgroundColor: ["#4C786B", "#9BD0C0", "#569EAC", "#ed5565"]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: "bottom", align: "start" } }
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
                    options: {
                        responsive: true,
                        plugins: { legend: { position: "bottom", align: "start" } }
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
                        maxBarThickness: 40
                    }]
                }
            });
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

            // Build query string
            const params = new URLSearchParams({
                from_date: startDate,
                to_date: endDate,
                booking_status: bookingStatus,
                payment_status: paymentStatus
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

    });

})(jQuery_1_8_2);
