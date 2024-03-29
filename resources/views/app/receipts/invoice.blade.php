<html>
	<head>
		<meta charset="utf-8">
		<title>{{ $name }}</title>
        <style>
            *
            {
                border: 0;
                box-sizing: content-box;
                color: inherit;
                font-family: inherit;
                font-size: inherit;
                font-style: inherit;
                font-weight: inherit;
                line-height: inherit;
                list-style: none;
                margin: 0;
                padding: 0;
                text-decoration: none;
                vertical-align: top;
            }

            /* heading */

            h1 { font: bold 100% sans-serif; letter-spacing: 0.5em; text-align: center; text-transform: uppercase; }

            /* table */

            table { font-size: 75%; table-layout: fixed; width: 50%; }
            table { border-collapse: separate; border-spacing: 1px; }
            th, td { border-width: 1px; padding: 0.5em; position: relative; text-align: left; }
            th, td { border-radius: 0.25em; border-style: solid; }
            th { background: #EEE; border-color: #BBB; }
            td { border-color: #DDD; }

            /* page */

            html { font: 16px/1 'Open Sans', sans-serif; overflow: auto; padding: 0.5in; }
            html { background: #999; cursor: default; width: 75%; }

            body { box-sizing: border-box; height: 11in; margin: 0 auto; overflow: hidden; padding: 0.5in; width: 8.5in; }
            body { background: #FFF; border-radius: 1px; box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5); }

            /* header */

            header { margin: 0 0 3em; }
            header:after { clear: both; content: ""; display: table; }

            header h1 { background: #074081; border-radius: 0.25em; color: #FFF; margin: 0 0 1em; padding: 0.5em 0; }
            header address { float: left; font-size: 75%; font-style: normal; line-height: 1.25; margin: 0 1em 1em 0; }
            header address p { margin: 0 0 0.25em; }
            header span, header img { display: block; float: right; }
            header span { margin: 0 0 1em 1em; max-height: 25%; max-width: 60%; position: relative; }
            header img { max-height: 100%; max-width: 100%; }
            header input { cursor: pointer; -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)"; height: 100%; left: 0; opacity: 0; position: absolute; top: 0; width: 100%; }

            /* article */

            article, article address, table.meta, table.inventory { margin: 0 0 3em; }
            article:after { clear: both; content: ""; display: table; }
            article h1 { clip: rect(0 0 0 0); position: absolute; }

            article address { float: left; font-size: 125%; font-weight: bold; }

            /* table meta & balance */

            table.meta, table.balance { float: right; width: 36%; }
            table.meta:after, table.balance:after { clear: both; content: ""; display: table; }

            /* table meta */

            table.meta th { width: 40%; }
            table.meta td { width: 60%; }

            /* table items */

            table.inventory { clear: both; width: 100%; }
            table.inventory th { font-weight: bold; text-align: center; }

            table.inventory td:nth-child(1) { width: 17%; }
            table.inventory td:nth-child(3) { width: 35%; }
            table.inventory td:nth-child(2) { text-align: right; width: 12%; }
            table.inventory td:nth-child(4) { text-align: right; width: 17%; }

            /* table balance */

            table.balance th, table.balance td { width: 50%; }
            table.balance td { text-align: right; }

        </style>
	</head>
	<body style="width: 700px;">
		<header>
			<h1>Estado de cuenta</h1>
		</header>
		<article>

			<div>
				<p style="font-size: 30px;">{{ $receipt->client->name }}</p>
			</div>
			<table class="meta">
				<tr>
					<th><span>Factura #</span></th>
					<td><span>{{ $receipt->number }}</span></td>
				</tr>
				<tr>
					<th><span>Fecha</span></th>
					<td><span>{{ $receipt->real_date->format('Y-m-d') }}</span></td>
				</tr>
				<tr>
					<th><span>Total</span></th>
					<td><span id="prefix">$ </span><span>{{ number_format($total, 2) }}</span></td>
				</tr>
			</table>
			<table class="inventory">
				<thead>
					<tr>
                        <th><span>Producto</span></th>
                        <th><span>Fecha</span></th>
						<th><span>Concepto</span></th>
                        @if ($person)
                            <th><span>Solicitado por</span></th>
                        @endif
                        {{-- @if ($hours)
                            <th><span>Horas</span></th>
                        @endif --}}
						<th><span>Costo</span></th>¡
					</tr>
				</thead>
				<tbody>
                    @if (isset($results['payables']))
                        @foreach ($results['payables'] as $result)
                            <tr>
                                <td><span>{{ $result['product'] }}</span></td>
                                <td><span>{{ $result['date'] }}</span></td>
                                <td><span>{{ $result['description'] }}</span></td>
                                @if ($person)
                                    <td><span>{{ $result['person'] }}</span></td>
                                @endif
                                @if ($hours)
                                    <td><span>{{ $result['hours'] }}</span></td>
                                @endif
                                <td><span data-prefix>$ </span><span>{{ $result['cost'] }}</span></td>
                            </tr>
                        @endforeach
                    @endif
                    @if (isset($results['tickets']))
                        @foreach ($results['tickets'] as $result)
                            <tr>
                                <td><span>{{ $result['product'] }}</span></td>
                                <td><span>{{ $result['date'] }}</span></td>
                                <td><span>{{ $result['description'] }}</span></td>
                                @if ($person)
                                    <td><span>{{ $result['person'] }}</span></td>
                                @endif
                                {{-- @if ($hours)
                                    <td><span>{{ number_format($result['hours'], 1) }}</span></td>
                                @endif --}}
                                <td><span data-prefix>$ </span><span>{{ $result['cost'] }}</span></td>
                            </tr>
                        @endforeach
                    @endif
				</tbody>
			</table>
            <table class="balance">
				<tr>
					<th><span>Total</span></th>
					<td><span data-prefix>$ </span><span>{{ number_format($total, 2) }}</span></td>
				</tr>
			</table>
		</article>

        <article>
            <header style="font-size: 14px;">
				<p>NURTECH</p>
				<p>Edilberto De Gracia</p>
				<p>RUC: 4-744-2500 D.V. 49</p>
			</header>
        </article>
	</body>
</html>
