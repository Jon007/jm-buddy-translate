/**
 * jm-buddy-translate translation helper library
 *
 * @package jm-buddy-translate
 * @link https://github.com/Jon007/jm-buddy-translate/tree/master/js/
 *
 * @author Jonathan Moore <jonathan.moore@bcs.org>
 * @version 1.0.1
 * @since 1.0
 */

/**
 * hide previous language translation result
 */
function btMinimize(){
	jQuery("#bTranslateContainer").css("display","none");
}

/**
 * master translation function: full auto default to translate any text selection.
 * @param {element} inputEl - optional element to use as source
 * @param {element} parentEl - optional element to use as parent to append translation result
 * @param {bool} useSelection - search for selected text (TODO: currently always on)
 * @param {string} sourceLang - source language, defaults to 'auto'
 * @param {string} targetLang - source language, defaults to document language
 * @return {bool} - success (does not return translation result, only launches async call)
 */
function btTranslate(inputEl, parentEl, useSelection, sourceLang, targetLang){
	//rewrite to remove parameter default values for Internet Explorer compatiblity
	if (!inputEl){inputEl=document.activeElement;}
	if (!parentEl){parentEl=jQuery("#wpadminbar");}
	if (!useSelection){useSelection=true;}
	if (!sourceLang){sourceLang='auto';}
	if (!targetLang){targetLang=document.documentElement.lang;}

	if ('string'===typeof(inputEl)){
		inputEl=jQuery("#" + inputEl);
	}

	var sourceText=btGetSelected(inputEl);
	if ('object'===typeof(sourceText)){
		sourceText=sourceText.toString();
	}
	if (!(sourceText)){sourceText='please select some text before translating';}
	return btGoogleGet(parentEl, sourceText, sourceLang, targetLang);
}

/**
 * called from Transate bbPress button, to translate a particular message
 * @param {element} el - current element (eg button that was clicked)
 * @return {bool} success (does not return translation result, only launches async call)
 */
function btnTranslatebbp(el){
	messageParent=el.parents('li');
	if (messageParent){
		//find reply content
		messageDetail=messageParent.find('.bbp-reply-content');
		if ( (messageDetail) && (messageDetail.length>0) ) {
			el=messageDetail[0];
		}	else{
			//if not reply content, fallback to parent
			el=messageParent[0];				
		}
		return btTranslate(el, messageParent);
	}else{
		//no activity parent? reset to document
		el=document.activeElement;
		return btTranslate(el);
	}
}

/**
 * called from Transate BuddyPress message button, to translate a particular message
 * @param {element} el - current element (eg button that was clicked)
 * @return {bool} success (does not return translation result, only launches async call)
 */
function btnTranslateMessage(el){
	messageParent=el.parents('div.message-box');
	if (messageParent){
		//check first is there a textarea (if it is a new message)
		messageDetail=messageParent.find('textarea');
		if ( (messageDetail) && (messageDetail.length>0) ) {
			el=messageDetail[0];
		}	else{
			messageDetail=messageParent.children('div.message-content');
			if ( (messageDetail) && (messageDetail.length>0) ) {
				el=messageDetail[0];
			}else{
				el=messageParent[0];
			}
		}
		return btTranslate(el, messageParent);
	}else{
		//no activity parent? reset to document
		el=document.activeElement;
		return btTranslate(el);
	}
}
	
/**
 * called from Transate BuddyPress activity button, find and translate activity detail
 * @param {element} el - current element (eg button that was clicked)
 * @return {bool} success (does not return translation result, only launches async call)
 */
function btnTranslateActivity(el){
	activityParent=el.parents('div.activity-content');
	if (activityParent){
		//if the activity is a message it should have an inner
		activityDetail=activityParent.children('div.activity-inner');
		if (! (activityDetail) || ( activityDetail.length === 0 ) ){
			activityDetail=activityParent.children('div.activity-header');
		}
		if (activityDetail){
			el=activityDetail[0];
		}
		else{
			el=activityParent[0];
		}
		return btTranslate(el, activityParent);
	}
	else{
		//no activity parent? reset to document
		el=document.activeElement;
		return btTranslate(el);
	}
}


/**
 * find text to translate, searching for selections, input elements and iframe content
 * @param {element} el - current element (defaults to document.activeElement)
 * @param {window} w - defaults to current window, also called for iframe window
 * @param {document} d - defaults to current document
 * @param {int} cstack - limit recursive searching
 * @return {string} selected string
*/
function btGetSelected(el, w, d, cstack){
	//rewrite to remove parameter default values for Internet Explorer compatiblity
	if (!el){el=document.activeElement;}
	if (!w){w=window;}
	if (!d){d=document;}
	if (!cstack){cstack=0;}

	//if a user has selected text, get that first: the text may span multiple elements
	//so is got from the document/window, not limited to the current element
	var t = '';
	if(w.getSelection){
		t = w.getSelection();
	}else if(d.getSelection){
		t = d.getSelection();
	}else if(d.selection){
		t = d.selection.createRange().text;
	}
	if ('object'===typeof(t)){
		t=t.toString();
	}

	//if there is no selection, use the current element
	if (! t || ''===t){
		//if the current element is an iframe there might be selected text on the iframe window
		//which is not detected by the previous code
		if (el){
			switch (el.tagName.toUpperCase()){
				case "": return '';  //avoid accidental translation of entire body when nothing is selected
				case "HTML": return '';  //avoid accidental translation of entire body when nothing is selected
				case "BODY": return '';  //avoid accidental translation of entire body when nothing is selected
				case "INPUT": return el.value;
				case "TEXTAREA": return el.value;
				case "IFRAME":
					if (cstack <2 ) {
						t = btGetSelected(el, el.contentWindow, el.contentDocument, cstack+1);
						//if no selection in the iframe, get the iframe current content
						if (! t || ''===t){
							innerEl = el.contentDocument.activeElement;
							if (innerEl){
								t = innerEl.innerText;
							}
							else{
								t = el.contentDocument.body.innerText;
							}
						}
					}
					return t;
				default: t = el.innerText;				
			}//case
		}//if el
	}//if !t
	return t;
}//func
	
/**
 * extract translation from Google translate JSON-like response: 
 * JQuery fails to parse due to trailing blank elements so here a regex approach is used
 * @param {string} str - Google translate JSON-like response
 * @return {string} extracted translation string
*/
function btExtractText( str ){
	var ret = "";
	if ( /"/.test( str ) ){
		//global search for all quoted strings
		matchResult = str.match( /"(.*?)"/g );

		//remove the last matches which are language codes...
		if (matchResult.length<3) {
			ret += matchResult[0]; 
		} else {
			for (i = 0; i < matchResult.length-3; i=i+2) { 
				//depending on usage a different treatment might be wanted for line breaks
				//ret += matchResult[i].toString().replace(/\\n/g, "<br/>");
				//remove starting/trailing quotes on the match and concatenate
				sResult = matchResult[i].replace (/(^")|("$)/g, '').trim();
				if ( (sResult==='\n') || (sResult==='\r') ){
					//ignore extra breaks - spotted in Internet Explorer results
				}else{
					ret += sResult;
				}
			}
		}
	} else {  //text has no quoted strings
		if ('object'===typeof(t)){
			t=t.toString();
		}else{
			ret = str;
		}
	}
	return ret;
}
	
/**
 * Get translation from google.
 * @param {element} parentEl - optional element to use as parent to append translation result
 * @param {string} q - text to translate
 * @param {string} sourceLang - source language, defaults to 'auto'
 * @param {string} targetLang - target language, defaults to document language
 * @return {bool} does not return translation result, only launches async call
 */
function btGoogleGet(parentEl, q, sourceLang, targetLang) {
		//rewrite to remove parameter default values for Internet Explorer compatiblity
		if (!parentEl){parentEl=jQuery("#wpadminbar");}
		if (!q){q='please select text to translate';}
		if (!sourceLang){sourceLang='auto';}
		if (!targetLang){targetLang=document.documentElement.lang;}

		startSpinner();
		/* LanguageApp would be the Google Apps way to do it */
		//var translatedText = LanguageApp.translate(sourceText, sourceLang, targetLang)
		/* URL Option */  
		var url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=" 
							+ sourceLang + "&tl=" + targetLang + "&dt=t&q=" + encodeURI(q);
		//example url: "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=es&dt=t&q=translate%20me"
		var href="http://translate.google.cn/#" + mapGoogleLanguageCode(sourceLang) + '/' + mapGoogleLanguageCode(targetLang) + '/' + encodeURI(q);
		jQuery('#bTranslateLink').attr('href',href);

		// Start timer, eg show spinner images, eg spin language icon
		var msBeforeAjaxCall = new Date().getTime();
		jQuery.ajax(url, {
				"type": "GET",
				"timeout": 9000  //increased timeout due to poor initial DNS lookups from china to translate.googleapis.com
		})
		.done(function (data, textStatus, jqXHR) {
				// Process data, as received in data parameter
				//actually if we get here the result may be valid JSON, so could be parsed as such
				//but this has not happened...
				translateResult=btExtractText(data);

				// Send warning log message if response took longer than 2 seconds
				/*
				var msAfterAjaxCall = new Date().getTime();
				var timeTakenInMs = msAfterAjaxCall - msBeforeAjaxCall;
				if (timeTakenInMs > 2000) {
					//warning
				}
				*/
		})
		.fail(function (jqXHR, textStatus, errorThrown) {
				// Request failed. may need to Show error message to user. 
				// errorThrown has error message, or "timeout" in case of timeout.
				var translateResult='';
				switch (textStatus){
					case "parsererror": break;  //expected JSON error, ignore
					case "timeout": translateResult='Translation timed out.\n'; break;
					default: translateResult=' Failed on: ' + textStatus + '\n';				
				}//case
				if ("timeout"!==textStatus){
					//request is expected to fail as google returns trailing empty JSON 
					//so we use RegEx to extract the text from the response 
					translateResult+=btExtractText(jqXHR.responseText);
				}
				var element = jQuery('#bTranslateResult');
				element.text(translateResult);
				element.html(element.text().replace(/\\n\\n/g,'<br />').replace(/\\n/g,'<br />'));
				element = jQuery('#bTranslateContainer').detach();
				parentEl.append(element);
				element.css("display","block");
			})
		.always(function(jqXHR, textStatus, errorThrown) {
				// finished eg Hide spinner image
				stopSpinner();
		});	
	return true;
}

/**
 * spin the translation logo (on start of translate ajax call)
 */
function startSpinner(){
	jQuery('#translate-icon').addClass('spinner');  
	//spinner class has this type of effect
	//jQuery('#translate-icon').css('animation', 'spin 3s linear infinite'); 
}
/**
 * stop spinning the translation logo (on end of translate ajax call)
 */
function stopSpinner(){
	jQuery('#translate-icon').removeClass('spinner');
}

/**
 * convert Wordpress language code (eg 'en-US') to google language code (eg 'en') 
 * for most languages except chinese, google translate links need 2 letter language codes
 * @param {string} language - input language string 
 * @return {string} mapped language code
 */
function mapGoogleLanguageCode(language){
	//rewrite to remove parameter default values for Internet Explorer compatiblity
	if (!language){language='auto';}
	switch (language){
		case "auto":
		case "zh-TW":
		case "zh-CN":
			return language;
		default:
			if (language.length>2){
				return language.substr(0,2);
			}
			else{
				return language;
			}
	}
}


/**
	* attach translation action to menu bar
	* attach action to mousedown rather than click to avoid losing current focus element
  * @param {jQuery} $ - WP includes JQuery in compatibility mode so $ global alias is not available unless passed in construct like this
 */
jQuery(document).ready(function($) {
	$("#wp-admin-bar-jm-buddy-translate").mousedown(function() {
			btTranslate();
	});

});
