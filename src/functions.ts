export function isPC(): boolean {
    let userAgentInfo = navigator.userAgent;
    let Agents = new Array("Android", "iPhone", "SymbianOS", "Windows Phone", "iPad", "iPod");  
    let flag = true;  
    for (let v = 0; v < Agents.length; v++) {  
        if (userAgentInfo.indexOf(Agents[v]) > 0) { flag = false; break; }  
    }  
    return flag; 
}

export function toChineseNumber(type: "upper" | "lower", number: number): string {
    if (number < 0 || number > 10 || number % 1 !== 0) return "";
    enum ChineseNumberLowerCase {
        "零", "一", "二", "三", "四", "五", "六", "七", "八", "九", "十"
    }
    enum ChineseNumberUpperCase {
        "零", "壹", "贰", "叁", "肆", "伍", "陆", "柒", "捌", "玖", "拾"
    }
    return type === "upper" ? ChineseNumberUpperCase[number] : ChineseNumberLowerCase[number];
}

export function getLinks(string: string): RegExpMatchArray | null | [] {
    let reg = new RegExp(/https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)/);
    return string.match(reg) !== null ? string.match(reg) : [];
}