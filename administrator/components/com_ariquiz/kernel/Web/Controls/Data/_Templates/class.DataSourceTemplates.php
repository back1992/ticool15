<?php
define('ARI_DATASOURCEDEF_TEMPLATE',
<<<ARI_DATASOURCEDEF_TEMPLATE_
(function()
{
	this.ds = new YAHOO.util.DataSource(%s);
	this.ds.connXhrMode = "queueRequests";
	this.ds.responseType = %s;
	this.ds.responseSchema = %s;
	return this.ds;
})();
ARI_DATASOURCEDEF_TEMPLATE_
);
?>