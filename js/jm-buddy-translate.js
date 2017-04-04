/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//hide previous language translation result
	function btMinimize(){
		//debugger
		jQuery("#bTranslateContainer").css("display","none");
	}
	//master translation function: call in here, full auto default to translate any text selection
	function btTranslate(inputEl=document.activeElement, parentEl=jQuery("#wpadminbar"), useSelection=true, sourceLang='auto', targetLang=document.documentElement.lang){
		if ('string'==typeof(inputEl)){
			inputEl=jQuery("#" + inputEl);
		}

		var sourceText=btGetSelected(inputEl);
		if ('object'==typeof(sourceText)){
			sourceText=sourceText.toString();
		}
		if (!(sourceText)){sourceText='please select some text before translating';}
		result = btGoogleGet(parentEl, sourceText, sourceLang, targetLang);
	}

	//call from Transate bbPress button, find the detail for the parent activity
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
			btTranslate(el, messageParent);
		}else{
			//no activity parent? reset to document
			el=document.activeElement;
			btTranslate(el);
		}
	}
	//call from Transate message button, find the detail for the parent activity
	function btnTranslateMessage(el){
		messageParent=el.parents('div.message-box');
		if (messageParent){
			//check first is there a textarea (if it is a new message)
			messageDetail=messageParent.find('textarea');
			//debugger
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
			btTranslate(el, messageParent);
		}else{
			//no activity parent? reset to document
			el=document.activeElement;
			btTranslate(el);
		}
	}
	
	//call from Transate activity button, find the detail for the parent activity
	function btnTranslateActivity(el){
		activityParent=el.parents('div.activity-content');
		if (activityParent){
			//if the activity is a message it should have an inner
			activityDetail=activityParent.children('div.activity-inner');
			if (! (activityDetail) || ( activityDetail.length == 0 ) ){
				activityDetail=activityParent.children('div.activity-header');
			}
			if (activityDetail){
				el=activityDetail[0];
			}
			else{
				el=activityParent[0];
			}
			btTranslate(el, activityParent);
		}
		else{
			//no activity parent? reset to document
			el=document.activeElement;
			btTranslate(el);
		}
	}


	/* get the text to translate */
	function btGetSelected(el=document.activeElement, w = window, d = document, cstack=0){
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
		if ('object'==typeof(t)){
			t=t.toString();
		}
		
		//if there is no selection, use the current element
		if (! t || ''==t){
			//if the current element is an iframe there might be selected text on the iframe window
			//which is not detected by the previous code
			if (el){
				switch (el.tagName){
					case "BODY": return '';  //avoid accidental translation of entire body when nothing is selected
			    case "INPUT": return el.value;
			    case "TEXTAREA": return el.value;
					case "IFRAME":
						if (cstack <2 ) {
							t = btGetSelected(el, el.contentWindow, el.contentDocument, cstack+1);
							//if no selection in the iframe, get the iframe current content
							if (! t || ''==t){
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
					default: return el.innerText;				
				}//case
			}//if el
		}//if !t
		return t;
	}//func
	
  /* extract text from Google translate JSON-like response */
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
					ret += matchResult[i].replace (/(^")|("$)/g, '');
				}
			}
		} else {  //text has no quoted strings
			if ('object'==typeof(t)){
				t=t.toString();
			}else{
				ret = str;
			}
		}
		return ret;
	}
	
	/* get translation from google */
	function btGoogleGet(parentEl=jQuery("#wpadminbar"), q='please select text to translate', sourceLang='auto', targetLang='') {

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
				"timeout": 5000
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
					default: debugger; translateResult=' Failed on: ' + textStatus + '\n';				
				}//case
				
				//request is expected to fail as google returns trailing empty JSON 
				//so we use RegEx to extract the text from the response 
				translateResult+=btExtractText(jqXHR.responseText);
				//debugger
				var element = jQuery('#bTranslateResult');
				element.text(translateResult);
				element.html(element.text().replace(/\\n\\n/g,'<br />').replace(/\\n/g,'<br />'));
				element = jQuery('#bTranslateContainer').detach()
				parentEl.append(element);
				element.css("display","block");
			})
		.always(function(jqXHR, textStatus, errorThrown) {
				// finished eg Hide spinner image
				stopSpinner();
		});	
		return 
	}
	
	function startSpinner(){
		jQuery('#translate-icon').addClass('spinner');  
		//spinner class has this type of effect
		//jQuery('#translate-icon').css('animation', 'spin 3s linear infinite'); 
	}
	function stopSpinner(){
		jQuery('#translate-icon').removeClass('spinner');
	}

	//for most languages except chinese, google translate links prefer 2 letter language codes
	function mapGoogleLanguageCode(language='auto'){
		switch (language){
			case "auto":
			case "zh-TW":
			case "zh-CN":
				return language;
			default:
				if (language.length>2){
					return language.substr(0,2)
				}
				else{
					return language;
				}
		}
	}


  //attach action to mousedown rather than click so we can catch current focus element
	jQuery(document).ready(function($) {
    $("#wp-admin-bar-jm-buddy-translate").mousedown(function() {
        btTranslate();
    });
    
	});
