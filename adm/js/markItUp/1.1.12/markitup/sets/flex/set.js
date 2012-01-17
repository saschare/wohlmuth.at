// -------------------------------------------------------------------
// markItUp!
// -------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// -------------------------------------------------------------------
// Textile tags example
// http://en.wikipedia.org/wiki/Textile_(markup_language)
// http://www.textism.com/
// -------------------------------------------------------------------
// Feel free to add more tags
// -------------------------------------------------------------------
myFlexSettings = {
	nameSpace: 'textile',
	previewParserPath:	'', // path to your Textile parser
	onShiftEnter:		{keepDefault:false, replaceWith:'\n\n'},
	markupSet: [
		{name:'Heading 1', key:'1', openWith:'h1(!(([![Class]!]))!). ', placeHolder:'Your title here...', className:'heading1'},
		{name:'Heading 2', key:'2', openWith:'h2(!(([![Class]!]))!). ', placeHolder:'Your title here...', className:'heading2'},
		{name:'Heading 3', key:'3', openWith:'h3(!(([![Class]!]))!). ', placeHolder:'Your title here...', className:'heading3'},
		{name:'Heading 4', key:'4', openWith:'h4(!(([![Class]!]))!). ', placeHolder:'Your title here...', className:'heading4'},
		{name:'Heading 5', key:'5', openWith:'h5(!(([![Class]!]))!). ', placeHolder:'Your title here...', className:'heading5'},
		{name:'Heading 6', key:'6', openWith:'h6(!(([![Class]!]))!). ', placeHolder:'Your title here...', className:'heading6'},
		{separator:'---------------' },
		{name:'Bold', key:'B', closeWith:'*', openWith:'*', className:'strong'},
		{name:'Italic', key:'I', closeWith:'_', openWith:'_', className:'emphasis'},
		{separator:'---------------' },
		{name:'Bulleted list', openWith:'(!(* |!|*)!)', className:'bulletlist'}, 
		{name:'Numeric list', openWith:'(!(# |!|#)!)', className:'numericlist'}, 
		{separator:'---------------' },
		{name:'Picture', replaceWith:'.sc(Image:[![Index]!])', className:'image'}, 
		{name:'Link', replaceWith:'.sc(Link:[![Index]!])', className:'anchor'},
		{separator:'---------------' }
	]
}