<x-proposal-layout>

    <x-slot name="header">
        <div class="w-[calc(100%-1rem)]">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Projects
            </h2>
            <div class="text-right mt-[-25px] mr-5">
                <livewire:proposal-calculator :proposal="$proposal" />
            </div>
        </div>
    </x-slot>

    <style>
        .weekend{ background: #f4f7f4 !important;}
        .gantt_section_time {margin-bottom:5px;}
        .gantt_section_time .gantt_time_selects select {background-position: right 0.1rem center !important;}
        .gantt_section_time .gantt_time_selects select:nth-child(1) {width: 35px;}
        .gantt_section_time .gantt_time_selects select:nth-child(2) {width: 85px;}
        .gantt_section_time .gantt_time_selects select:nth-child(3) {width: 52px;}
        /* common styles for overriding borders/progress color */
        .gantt_task_line{ border-color: rgba(0, 0, 0, 0.25); }
        .gantt_task_line .gantt_task_progress { background-color: rgba(0, 0, 0, 0.25); }
        .gantt_task_line.high { background-color: #f40303; }
        .gantt_task_line.high .gantt_task_content { color: #fff; }
        .gantt_task_line.medium { background-color: #378122; }
        .gantt_task_line.medium .gantt_task_content { color: #fff; }
        .gantt_task_line.low { background-color: #e157de; }
        .gantt_task_line.low .gantt_task_content { color: #fff; }
    </style>

    <div id="gantt_here" class="h-screen w-[calc(100%-1rem)]"></div>
    <script type="text/javascript">
        var isMobile = {
            Android: function() {
                return navigator.userAgent.match(/Android/i);
            },
            BlackBerry: function() {
                return navigator.userAgent.match(/BlackBerry/i);
            },
            iOS: function() {
                return navigator.userAgent.match(/iPhone|iPad|iPod/i);
            },
            Opera: function() {
                return navigator.userAgent.match(/Opera Mini/i);
            },
            Windows: function() {
                return navigator.userAgent.match(/IEMobile/i) || navigator.userAgent.match(/WPDesktop/i);
            },
            any: function() {
                return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
            }
        };

        let width = 300;
        if (isMobile.any()) {
            width = 150;
        }

        gantt.config.date_format = "%Y-%m-%d %H:%i:%s";
        gantt.config.work_time = true;  // removes non-working time from calculations
        gantt.config.skip_off_time = true;    // hides non-working time in the chart

        gantt.config.columns = [
            {name:"text",       label:"Task name",  width:width, tree:true, resize:true},
            {name:"start_date", label:"Start time", align:"center", resize:true},
            {name:"hours",      label:"Hours",   align:"center", resize:true},
            {name:"add",        label:"",           width:44 }
        ];

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
            {name:"priority_id", height:34, map_to:"priority_id", type:"select", options:opts, default_value: 1},
            {name:"hours",       height:50, map_to:"hours", type:"textarea"},
            {name:"time",        height:35, map_to:"auto", type:"duration"},
        ];

        gantt.config.order_branch = true;// order tasks only inside a branch
        gantt.config.order_branch_free = true;


        gantt.init("gantt_here");


        var drag_id = null;
        gantt.attachEvent("onRowDragStart", function(id, target, e) {
            drag_id = id;
            return true;
        });
        gantt.attachEvent("onRowDragEnd", function(id, target) {
            drag_id = null;
            gantt.render();
        });

        gantt.templates.grid_row_class = function(start, end, task){
            if(drag_id && task.id != drag_id){
                if(task.$level != gantt.getTask(drag_id).$level)
                    return "cant-drop";
                }
            return "";
        };

        gantt.load('/proposal/tasks/{{ $proposal->id }}')

        gantt.templates.task_class  = function(start, end, task){
            switch (task.priority_id){
                case 1:
                    return "high";
                    break;
                case 2:
                    return "medium";
                    break;
                case 3:
                    return "low";
                    break;
            }
        };

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
