@extends("layouts.main")

@section("mainData")
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
        <div class="border-bottom mb-3 pt-3 pb-2">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                <h1 class="h2">{{ $event->name }}</h1>
            </div>
            <span class="h6">{{ date('F d, Y', strtotime($event->date)) }}</span>
        </div>

        <div class="mb-3 pt-3 pb-2">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                <h2 class="h4">Room Capacity</h2>
            </div>
        </div>

        <!-- TODO create chart here -->
        <canvas id="report_chart" height="40%" width="100%"></canvas>

        <script>
            var ctx = document.getElementById('report_chart').getContext('2d');

            var chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! $event->chart['title']->toJson() !!},
                    datasets: [
                        {
                            label: 'Attendees',
                            backgroundColor: {!! $event->chart['attendees_color']->toJson() !!},
                            data: {!! $event->chart['attendees']->toJson() !!}
                        },
                        {
                            label: 'Capacity',
                            backgroundColor: 'rgb(160, 200, 245)',
                            data: {!! $event->chart['capacity']->toJson() !!}
                        }
                    ]
                },
                options: {
                    scales: {
                        xAxes: [{
                            scaleLabel: {
                                display: true,
                                labelString: 'Sessions'
                            }
                        }],
                        yAxes: [{
                            scaleLabel: {
                                display: true,
                                labelString: 'Capacity'
                            }
                        }]
                    },
                    legend: {
                        display: true,
                        position: 'right'
                    }
                }
            });
        </script>
    </main>
@endsection
