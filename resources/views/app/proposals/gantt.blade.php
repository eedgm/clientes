<x-proposal-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Projects
        </h2>
        <div class="text-right mt-[-25px] mr-5">
            <livewire:proposal-calculator :proposal="$proposal" />
        </div>
    </x-slot>

    <style>
        .weekend{ background: #f4f7f4 !important;}
        .gantt_section_time {margin-bottom:5px;}
        .gantt_section_time .gantt_time_selects select {background-position: right 0.1rem center !important;}
        .gantt_section_time .gantt_time_selects select:nth-child(1) {width: 35px;}
        .gantt_section_time .gantt_time_selects select:nth-child(2) {width: 85px;}
        .gantt_section_time .gantt_time_selects select:nth-child(3) {width: 52px;}
    </style>

    <div id="gantt_here" class="w-screen h-screen"></div>
    <script type="text/javascript">
        gantt.config.date_format = "%Y-%m-%d %H:%i:%s";
        gantt.config.work_time = true;  // removes non-working time from calculations
        gantt.config.skip_off_time = true;    // hides non-working time in the chart

        gantt.templates.scale_cell_class = function(date){
            if(date.getDay()==0||date.getDay()==6){
                return "weekend";
            }
        };
        gantt.templates.timeline_cell_class = function(task,date){
            if(date.getDay()==0||date.getDay()==6){
                return "weekend" ;
            }
        };

        var opts = [
            { key: 1, label: 'High' },
            { key: 2, label: 'Normal' },
            { key: 3, label: 'Low' }
        ];

        gantt.locale.labels.section_priority_id = "Priority";
        gantt.locale.labels.section_hours = "Hours";

        gantt.config.lightbox.sections = [
            {name:"description", height:50, map_to:"text", type:"textarea", focus:true},
            {name:"priority_id",    height:34, map_to:"priority_id", type:"select", options:opts, default_value: 1},
            {name:"hours",       height:50, map_to:"hours", type:"textarea"},
            {name:"time",        height:35, map_to:"auto", type:"duration"},
        ];

        gantt.init("gantt_here");

        gantt.load('/proposal/tasks/{{ $proposal->id }}')

        var dp = gantt.createDataProcessor(function(entity, action, data, id) {
            data._token = "{{ csrf_token() }}"
            data.proposal_id = '{{ $proposal->id }}'
            data.start_date = getFormattedDate(data.start_date)
            switch(action) {
                case "create":
                return gantt.ajax.post(
                        "/tasks/" + entity,
                        data
                );
                break;
                case "update":
                return gantt.ajax.put(
                        "/tasks/" + entity + "/update/" + id,
                        data
                    );
                break;
                case "delete":
                return gantt.ajax.put(
                        "/tasks/" + entity + "/delete/" + id,
                        data
                );
                break;
            }
        });

        function getFormattedDate(date) {

                const dt = new Date(date);
                    const padL = (nr, len = 2, chr = `0`) => `${nr}`.padStart(2, chr);
                    return `${
                        dt.getFullYear()}-${
                        padL(dt.getMonth()+1)}-${
                        padL(dt.getDate())} 00:00:00`
            }
    </script>

</x-proposal-layout>
