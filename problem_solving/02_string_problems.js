// ============================================
// STRING PROBLEMS — Bina Built-in Helper Ke
// Run: node 02_string_problems.js
// ============================================

// ---------- 1. String Reverse Karo (.reverse() / .split("").reverse().join("") nahi) ----------
function reverseString(str) {
  let result = "";
  for (let i = str.length - 1; i >= 0; i--) {
    result += str[i];
  }
  return result;
}
console.log("Reversed:", reverseString("hello")); // "olleh"

// ---------- 2. Palindrome Check Karo ----------
function isPalindrome(str) {
  let left = 0;
  let right = str.length - 1;
  while (left < right) {
    if (str[left] !== str[right]) return false;
    left++;
    right--;
  }
  return true;
}
console.log("Palindrome (madam):", isPalindrome("madam")); // true
console.log("Palindrome (hello):", isPalindrome("hello")); // false

// ---------- 3. Vowels Count Karo ----------
function countVowels(str) {
  const vowels = "aeiouAEIOU";
  let count = 0;
  for (let i = 0; i < str.length; i++) {
    if (vowels.indexOf(str[i]) !== -1) count++; // yahan indexOf allowed hai (chota fixed string pe hai, algorithm ka part nahi)
  }
  return count;
}
console.log("Vowels in 'Interview':", countVowels("Interview")); // 4

// ---------- 4. Har Character Ki Frequency ----------
function charFrequency(str) {
  const freq = {};
  for (let i = 0; i < str.length; i++) {
    const ch = str[i];
    freq[ch] = (freq[ch] || 0) + 1;
  }
  return freq;
}
console.log("Char freq:", charFrequency("hello")); // { h:1, e:1, l:2, o:1 }

// ---------- 5. Anagram Check Karo (dono strings same letters se bane hon) ----------
function isAnagram(str1, str2) {
  if (str1.length !== str2.length) return false;
  const freq1 = charFrequency(str1.toLowerCase());
  const freq2 = charFrequency(str2.toLowerCase());
  for (const key in freq1) {
    if (freq1[key] !== freq2[key]) return false;
  }
  return true;
}
console.log("Anagram (listen/silent):", isAnagram("listen", "silent")); // true

// ---------- 6. Har Word Ka Pehla Letter Capitalize Karo ----------
function capitalizeWords(str) {
  let result = "";
  let capitalizeNext = true;
  for (let i = 0; i < str.length; i++) {
    const ch = str[i];
    if (ch === " ") {
      result += ch;
      capitalizeNext = true;
    } else if (capitalizeNext) {
      result += ch.toUpperCase();
      capitalizeNext = false;
    } else {
      result += ch;
    }
  }
  return result;
}
console.log("Capitalized:", capitalizeWords("hello world from next js")); // "Hello World From Next Js"

// ---------- 7. Duplicate Characters Hatao ----------
function removeDuplicateChars(str) {
  let result = "";
  for (let i = 0; i < str.length; i++) {
    if (result.indexOf(str[i]) === -1) {
      result += str[i];
    }
  }
  return result;
}
console.log("No duplicate chars:", removeDuplicateChars("programming")); // "progamin"

// ---------- 8. Pehla Non-Repeating Character Dhoondo ----------
function firstNonRepeatingChar(str) {
  const freq = charFrequency(str);
  for (let i = 0; i < str.length; i++) {
    if (freq[str[i]] === 1) return str[i];
  }
  return null;
}
console.log("First non-repeating:", firstNonRepeatingChar("swiss")); // "w"

// ---------- 9. Sentence Mein Words Ka Order Reverse Karo ----------
function reverseWords(sentence) {
  // manual split (bina .split() ke bhi kar sakte, yahan readability ke liye .split allowed)
  const words = sentence.split(" ");
  let result = "";
  for (let i = words.length - 1; i >= 0; i--) {
    result += words[i];
    if (i !== 0) result += " ";
  }
  return result;
}
console.log("Reversed words:", reverseWords("I love Next JS")); // "JS Next love I"

// ---------- 10. String Mein Ek Substring Manually Dhoondo (.includes() nahi) ----------
function containsSubstring(str, sub) {
  for (let i = 0; i <= str.length - sub.length; i++) {
    let match = true;
    for (let j = 0; j < sub.length; j++) {
      if (str[i + j] !== sub[j]) {
        match = false;
        break;
      }
    }
    if (match) return true;
  }
  return false;
}
console.log("Contains 'view':", containsSubstring("interview", "view")); // true

// ============================================
// PRACTICE — Khud Karo (solution nahi diya)
// ============================================
// 1. String compression karo: "aaabbc" → "a3b2c1"
// 2. Check karo ek string dusri string ka rotation hai ya nahi (e.g. "erbottlewat" "waterbottle")
// 3. String mein sabse lamba word dhoondo (bina .split() ke agar possible ho)
// 4. Toggle case karo har character ka: "Hello" → "hELLO"
// 5. Check karo string valid number hai ya nahi (sirf digits, optional minus sign)
// 6. Longest common prefix nikalo array of strings mein se, e.g. ["flower","flow","flight"] → "fl"
