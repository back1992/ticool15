<?php
define('ARI_DATATABLE_TEMPLATE',
<<<ARI_DATATABLE_TEMPLATE_
<script type="text/javascript" language="javascript">
	var %1\$s = null;
	YAHOO.util.Event.onDOMReady(function() {
	        var myColumnDefs = %2\$s;
	        this.myDataSource = %3\$s;
	        var oConfig = %4\$s;

	        %1\$s = new YAHOO.widget.%7\$s("%1\$s",
	                myColumnDefs, this.myDataSource, oConfig);

			var ds = %1\$s.getDataSource();
			var oldSendRequestHandler = ds.sendRequest;
			ds.sendRequest = function(oRequest, oCallback, oCaller)
			{
				oRequest = YAHOO.ARISoft.widgets.dataTable.updateRequest(oRequest);
				oldSendRequestHandler.call(this, oRequest, oCallback, oCaller);
	        };
	        %1\$s.subscribe("rowMouseoverEvent", %1\$s.onEventHighlightRow);
	        %1\$s.subscribe("rowMouseoutEvent", %1\$s.onEventUnhighlightRow);
	        %5\$s
	        %1\$s.sortColumn = YAHOO.ARISoft.widgets.dataTable.sortColumn;
	        %6\$s;
	});
</script>
ARI_DATATABLE_TEMPLATE_
);
?>