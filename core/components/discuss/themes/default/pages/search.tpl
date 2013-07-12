
<form class="m-fullw-form m-styled-form h-group m-search" action="[[~[[*id]]]]search/" method="get">
	<h1>[[%discuss.search? &namespace=`discuss` &topic=`web`]]</h1>
    <div class="m-panel f1-f8">
        <div class="f1-f5 f-pad h-group">
            <label class="search" for="dis-search">[[%discuss.search]]:</label>
            <input class="search" type="text" id="dis-search" name="s" value="[[+search]]" />
        </div>
        <div class="f-all f-pad  h-group">
            <a id="dis-search-advanced-toggle" href="a-search-adavnaced">[[%discuss.search_advanced_options]]</a>
        </div>
        <div id="dis-search-advanced" class="f-all m-grouped-content">
            <div class="f-full">
                <div class="f1-f4 f-pad">
                    <label for="dis-search-qa">[[%discuss.post_type]]
                        <span class="error">[[+error.board]]</span>
                    </label>
                    <select name="dis_search_qa" id="dis-search-qa">
                        <option value="1" [[+dis_search_qa:eq=`1`:then=`selected="selected"`]]>[[%discuss.all_posts]]</option>
                        <option value="2" [[+dis_search_qa:eq=`2`:then=`selected="selected"`]]>[[%discuss.discussions]]</option>
                        <option value="3" id="QA" [[+dis_search_qa:eq=`3`:then=`selected="selected"`]]>[[%discuss.questions]]</option>
                    </select>
                </div>
                <div id="SubOptions" class="f5-f8 sub-options">
                    <label for="dis-search-qa-opt">[[%discuss.question_options]]
                        <span class="error">[[+error.board]]</span>
                    </label>
                    <input type="radio" name="qa_options" value="" [[+qa_options:empty=`checked="checked"`]] id="qa-all-questions">[[%discuss.all_questions]]<!--<label for="qa-all-questions">[[%discuss.all_questions]]</label>-->
                    <input type="radio" name="qa_options" value="1" [[+qa_options:eq=`1`:then=`checked="checked"`]] id="qa-answered">[[%discuss.answered]]<!--<label for="qa-all-questions">[[%discuss.answered]]</label>-->
                    <input type="radio" name="qa_options" value="0" [[+qa_options:eq=`0`:then=`checked="checked"`]] id="qa-wo-answer">[[%discuss.wo_answer]]<!--<label for="qa-all-questions">[[%discuss.wo_answer]]</label>-->
                </div>
            </div>

            <div class="f-full">
                <div class="f1-f4 f-pad">
                    <label for="dis-search-board">[[%discuss.board]]:
                        <span class="error">[[+error.board]]</span>
                    </label>
                    <select name="board" id="dis-search-board">[[+boards]]</select>
                </div>
                <div class="f5-f8 f-pad">
                    <label for="dis-author">[[%discuss.author]]:</label>
                    <input type="text" id="dis-author" name="user" value="[[+user]]" class="autocomplete" data-autocomplete-action="rest/find_user" data-autocomplete-single="true" />
                </div>
            </div>

            <div class="f1-f4 f-pad">
                <label for="dis-date-start">[[%discuss.date_start]]:</label>
                <input type="text" id="dis-date-start" class="m-datepicker" name="date_start" value="[[+date_start]]"/>
            </div>

            <div class="f5-f8 f-pad">
                <label for="dis-date-end">[[%discuss.date_end]]:</label>
                <input type="text" id="dis-date-end" class="m-datepicker" name="date_end" value="[[+date_end]]"/>
            </div>
        </div>
        <div class="f1-f8 f-pad">
            <input type="submit" value="[[%discuss.search]]" />
        </div>
    </div>
</form>
[[+total:gte=`1`:then=`
    <header class="dis-cat-header dark-gradient h-group sticky-bar top">
        [[+results:notempty=`<h1>[[%discuss.search_results?total=`[[+total]]`&start=`[[+start]]`&end=`[[+end]]`]]</h1>`]]
        [[+pagination]]
    </header>

    <div class="dis-threads">
        <ul class="dis-list search-results">
            [[+results]]
        </ul>
    </div>
    <div class="paginate stand-alone bottom horiz-list">
    [[+pagination]]
    </div>
`:else=`
    [[+results]]
`]]
[[+bottom]]
