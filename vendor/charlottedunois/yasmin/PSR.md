# PSR Compliance

This table shows the compliance with the PSR 1 and 2.

| PSR | Rule                                                                     | Compliant | Notes                                                                           |
|-----|--------------------------------------------------------------------------|-----------|---------------------------------------------------------------------------------|
|  1  | Only <?php and <?= tags                                                  |     ✅    |                                                                                 |
|  1  | Files must use UTF8 w/o BOM                                              |     ✅    |                                                                                 |
|  1  | Only declares or side effects                                            |     ✅    |                                                                                 |
|  1  | Follows Autoloading PSR                                                  |     ✅    | PSR-4                                                                           |
|  1  | Class names declared in StudlyCase                                       |     ✅    |                                                                                 |
|  1  | Method names declared in camelCase                                       |     ✅    |                                                                                 |
|  1  | Class constants upper case + _                                           |     ✅    |                                                                                 |
|  2  | Files must use Unix Linefeeding character (LF)                           |     ✅    |                                                                                 |
|  2  | Files must end with one blank line                                       |     ✅    |                                                                                 |
|  2  | The closing PHP tag must be omitted                                      |     ✅    |                                                                                 |
|  2  | Lines length max. 120 characters (soft limit)                            |     ✅    |                                                                                 |
|  2  | No trailing whitespaces after non-blank lines                            |     ✅    |                                                                                 |
|  2  | Only one statement per line                                              |     ✅    |                                                                                 |
|  2  | Code must use 4 spaces as indentation                                    |     ✅    |                                                                                 |
|  2  | PHP keywords must be in lowercase                                        |     ✅    |                                                                                 |
|  2  | PHP constants (true, false, null,...) must be in lowercase               |     ✅    |                                                                                 |
|  2  | One blank line after namespace and use                                   |     ✅    |                                                                                 |
|  2  | Extends and implements on same line                                      |     ✅    |                                                                                 |
|  2  | Lists of implements may be split across multiple lines (one per line)    |     ⚠    | No limit per line                                                               |
|  2  | Opening braces for classes and methods on next line                      |     ❌    | Opening braces go on the same line with one space in between ) and {            |
|  2  | Closing braces for classes and methods on next line                      |     ✅    |                                                                                 |
|  2  | Visibility declared on all properties                                    |     ✅    |                                                                                 |
|  2  | Only one property declared per statement                                 |     ✅    |                                                                                 |
|  2  | Properties should not be prefixed with an underscore                     |     ✅    |                                                                                 |
|  2  | Visibility declared on all methods                                       |     ❌    | Public declaration is implied                                                   |
|  2  | Final and abstract declared before visiblity and static after            |     ✅    |                                                                                 |
|  2  | Methods should not be prefixed with an underscore                        |     ❌    |                                                                                 |
|  2  | Method opening parentheses have no space before and after them           |     ✅    |                                                                                 |
|  2  | Method closing parentheses have no space before them                     |     ✅    |                                                                                 |
|  2  | Method arguments have no space before the comma and one after the comma  |     ✅    |                                                                                 |
|  2  | Method arguments with default values at the end of the list              |     ✅    |                                                                                 |
|  2  | Method arguments have no space before the comma and one after the comma  |     ✅    |                                                                                 |
|  2  | Method arguments may be split across multiple lines with one per line    |     ⚠    | No argument limit per line                                                      |
|  2  | Control structures have one space after them                             |     ❌    | No space                                                                        |
|  2  | Method and function calls have no space between name and parentheses     |     ✅    |                                                                                 |
|  2  | Method and function args may be split across multiple lines with 1/line  |     ⚠    | No argument limit per line                                                      |
|  2  | Control struct. opening parentheses have no space after them             |     ❌    | There is no space before the opening parentheses (after keyword)                |
|  2  | Control struct. closing parentheses have no space before and after them  |     ✅    |                                                                                 |
|  2  | Control struct. closing parentheses and opening brace have one space     |     ✅    |                                                                                 |
|  2  | Control structucture body indented once                                  |     ✅    |                                                                                 |
|  2  | if, elseif and else, including spaces, parentheses and braces            |     ❌    | There is no space before the opening parentheses (after keyword)                |
|  2  | switch structure, including case and indentation                         |     ❌    | The break keyword must be indented at the same level as the case keyword        |
|  2  | try-catch-finally, including parentheses and braces                      |     ✅    |                                                                                 |
|  2  | Closures, including spaces, parentheses and braces                       |     ✅    |                                                                                 |
