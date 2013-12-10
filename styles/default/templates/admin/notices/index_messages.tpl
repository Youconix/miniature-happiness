<table>
    <tr>
        <th></th>
        <th></th>
        <th>{noticesTitle}</th>
        <th>{noticesDate}</th>
    </tr>
    <block {notice}>
    <tr>
        <td class="adminButton"><a href="javascript:adminNotices.viewNotice({id})"><img src="{NIV}images/view.png" alt="{viewText}" title="{viewText}"></a></td>
        <td class="adminButton"><a href="javascript:adminNotices.deleteLog({id})"><img src="{NIV}images/delete.png" alt="{deleteText}" title="{deleteText}"></a></td>
        <td>{title}</td>
        <td>{time}</td>
    </tr>
    </block>
</table>
