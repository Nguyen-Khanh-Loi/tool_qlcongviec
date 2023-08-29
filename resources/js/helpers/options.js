import { languageText } from "@/js/helpers/language";
export const options = {
    optionsTable : () => optionsDataTable,
}
const optionsDataTable = {
    responsive: true,
    autoWidth:false,
    select: true,
    ordering: false,
    info:     false,
    dom: 'Bftip',
    // paging: false,
    scroller: false,
    stateSave: true,
    language:{
        search:languageText.search_text,
        paginate:{
            first:languageText.first_page_text, 
            next:languageText.next_page_text, 
            previous:languageText.previous_page_text, 
            last:languageText.last_page_text
        }
    }
}