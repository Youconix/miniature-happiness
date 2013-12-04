<div id="faqAdmin">
	<h1>{faqTitle}</h1>

	<div class="adminPanel">
		<p>{currentLanguage} : <select id="language" onchange="adminFAQ.changeLanguage()">{languages}</select></p>

		<table>
		<thead>
		<tr>
			<td><br/></td>
			<td>{question}</td>
			<td>{answer}</td>
		</tr>
		</thead>
		<tbody id="faqList">
		<tr id="newFAQ">
			<td><a href="javascript:adminFAQ.add()"><img src="{style_dir}/images/add.png" alt="{add}"/></a></td>
			<td><input type="text" id="question" value=""/></td>
			<td><input type="text" id="answer" value=""/></td>
		</tr>
		<block {language}>
		<tr>
			<td><a href="javascript:adminFAQ.delete({id})"><img src="{style_dir}/images/delete.png" alt="{delete}"/></a></td>
			<td>{question}</td>
			<td>{answer}</td>
		</tr>
		</block>
		</tbody>
		</table>
	</div>
</div>
