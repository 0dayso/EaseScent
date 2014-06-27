<input type="text" size="30" onclick="" readonly name="dateline" id="inputtime" value="{echo date('Y-m-d H:i:s', time())}" class="measure-input input-date"/> 
<script type="text/javascript">
    Calendar.setup({
        inputField : "inputtime",
        trigger    : "inputtime",
        onSelect   : function() { this.hide() },
        showTime   : 24,
        dateFormat : "%Y-%m-%d %H:%M:%S"
    });
</script>