var PAGINATION_EVENTS_INITIALIZED=false;
function initializePaginationTables() {
    $("table.table-pagination[data-page]").each(function() {
        if($(this).find("tbody tr").length<=0) {
            $(this).find("tfoot .pagination").hide();
            $(this).find("tbody").html("<tr><th colspan=1000><h3 class='text-center'>No Records Found ...</h3></th></tr>");
            return;
        }
        $(this).find("thead th[name]").each(function() {
            nm=$(this).attr("name");
            ttl=$(this).text();
            $(this).closest("table").find("tfoot select[name=sort]").append("<option value='"+nm+" asc'>"+ttl+" Ascending</option>");
            $(this).closest("table").find("tfoot select[name=sort]").append("<option value='"+nm+" desc'>"+ttl+" Descending</option>");
        });
        
        //Generate Filters
        
        limt=$(this).closest("table.table-pagination").data('limit');
        $(this).closest("table.table-pagination").find('select[name=limit]').val(limt);
    });
    
    
    initializePaginationEvents();
}
function initializePaginationEvents() {
	if(!PAGINATION_EVENTS_INITIALIZED) {
		$("table.table-pagination[data-page]").delegate("tfoot nav .pagination .page-next","click",function() {
        func=$(this).closest("table.table-pagination").data('func');
        page=parseInt($(this).closest("table.table-pagination").data('page'));
        limt=$(this).closest("table.table-pagination").data('limit');
        
        sort=$(this).closest("table.table-pagination").find('select[name=sort]').val();
        limt=$(this).closest("table.table-pagination").find('select[name=limit]').val();
        
        pmax=$(this).closest("table.table-pagination").data('page_max');
        if(pmax==null) {
            pmax=1;
        }
        if(page>=pmax) page=pmax;
        else page++;
        
        $(this).closest("table.table-pagination").data('page',page);
        
        window[func](page,limt,sort);
    });
    $("table.table-pagination[data-page]").delegate("tfoot nav .pagination .page-prev","click",function() {
        func=$(this).closest("table.table-pagination").data('func');
        page=parseInt($(this).closest("table.table-pagination").data('page'));
        limt=$(this).closest("table.table-pagination").data('limit');
        
        sort=$(this).closest("table.table-pagination").find('select[name=sort]').val();
        limt=$(this).closest("table.table-pagination").find('select[name=limit]').val();
        
        if(page<=0) page=0;
        else page--;
        
        $(this).closest("table.table-pagination").data('page',page);
        
        window[func](page,limt,sort);
    });
    $("table.table-pagination[data-page]").delegate("tfoot nav .pagination .page-reload","click",function() {
        func=$(this).closest("table.table-pagination").data('func');
        page=$(this).closest("table.table-pagination").data('page');
        limt=$(this).closest("table.table-pagination").data('limit');
        
        sort=$(this).closest("table.table-pagination").find('select[name=sort]').val();
        limt=$(this).closest("table.table-pagination").find('select[name=limit]').val();
        
        page=0;
        $(this).closest("table.table-pagination").data('page',page);
        
        window[func](page,limt,sort);
    });
    
    $("table.table-pagination[data-page]").delegate("select[name=sort]","change",function() {
	    func=$(this).closest("table.table-pagination").data('func');
        page=$(this).closest("table.table-pagination").data('page');
        limt=$(this).closest("table.table-pagination").data('limit');
        
        sort=$(this).closest("table.table-pagination").find('select[name=sort]').val();
        limt=$(this).closest("table.table-pagination").find('select[name=limit]').val();
        
        page=0;
        
        $(this).closest("table.table-pagination").data('page',page);
        
        window[func](page,limt,sort);
		});
		$("table.table-pagination[data-page]").delegate("select[name=limit]","change",function() {
				func=$(this).closest("table.table-pagination").data('func');
					page=$(this).closest("table.table-pagination").data('page');
					limt=$(this).closest("table.table-pagination").data('limit');

					sort=$(this).closest("table.table-pagination").find('select[name=sort]').val();
					limt=$(this).closest("table.table-pagination").find('select[name=limit]').val();

					page=0;

					$(this).closest("table.table-pagination").data('page',page);

					window[func](page,limt,sort);
		});
		
		PAGINATION_EVENTS_INITIALIZED=true;
	}
}
function getTablePaginationFilters(tableRef) {
    q=[];
    $(tableRef).find("thead tr.filters th").each(function() {
        
    });
    return q.join("&");
}
