# Test Cases 5-6

## Test case 5.1, Navigate to Messages Page

Normal navigation to page, page is shown with Message Board.

### Input:

- Clear existing cookies
- Navigate to site and click link "Go to message board".

### Output:

- The text "Not logged in", is shown.
- A link "Go to login" is shown.
- A form for posting to the message board is shown.
- The text "Message Board" is shown.
- A table with messages is show with columns "Date", "Author" and "Message".
- Today's date is shown at the bottom of the page.

---

## Test case 5.2: Failed submit without any entered fields

Make sure message posting cannot happen without entering any fields.

### Input:

- Testcase 5.1
- Make sure both username and message are empty
- Press "Submit" button

### Output:

- Feedback: “Please enter a username” is shown.
- A form for posting a message is shown.

---

## Test case 5.3: Failed submit with only username

Make sure message posting cannot happen without entering both fields.

### Input:

- Testcase 5.1
- Enter a username "abc" and let message be empty
- Press "Submit" button

### Output:

- Feedback: “Your message is empty!” is shown
- A form for posting a message is shown.
- "abc" is filled in as username

---

## Test case 5.4: Failed submit with only message

Make sure message posting cannot happen without entering both fields.

### Input:

- Testcase 5.1
- Enter a message "message" and let Username be empty
- Press "Submit" button

### Output:

- Feedback: “Please enter a username” is shown
- A form for posting a message is shown.
- "message" is filled in as message.

---

## Test case 5.5: Failed submit with existing username

Make sure message posting cannot happen with an existing username.

### Input:

- Testcase 5.1
- Enter username "Admin" and message "message".
- Press "Submit" button

### Output:

- Feedback: "Username already exists. Pick another!" is shown
- A form for login is shown.
- A form for posting a message is shown.
- "message" is filled in as message.
- "Admin" is filled in as username

---

## Test case 5.6: Successful post with unique username and a message

Make sure message posting will happen if correct username and a message is filled in.

### Input:

- Testcase 5.1
- Enter a unique hashed username like "97bafdcc028537f02f5aad11f805c6c9" and message "message"
- Press "Submit" button

### Output:

- Feedback: "Your message was submitted!" is shown.
- The message appears at the top of the Message Board with the entered username followed by "(as guest)" as Author and "message" as Message, with the correct date.
- A form for posting a message is shown.
- The entered username is filled in as username.

---

## Test case 5.7: Navigate to Messages Page as Logged in user

### Input:

- TestCase 1.7: Successful login.
- Click link "Go to message board".

### Output:

- The text "Logged in", is shown.
- A link "Account" is shown.
- A form for posting to the message board is shown.
- There is no input for username. Instead the username "Admin" is shown.
- The text "Message Board" is shown.
- A table with messages is show with columns "Date", "Author" and "Message".
- Today's date is shown at the bottom of the page.

---

## Test case 5.8: Failed submit as logged in without message

Make sure message posting cannot happen without entering something in the message field.

### Input:

- Testcase 5.7
- Make sure message is empty
- Press "Submit" button

### Output:

- Feedback: “Your message is empty!” is shown.
- A form for posting a message is shown.

---

## Test case 5.9: Successful post as logged in user

Make sure message posting will happen if message is filled in.

### Input:

- Testcase 5.7
- Enter a message "message"
- Press "Submit" button

### Output:

- Feedback: "Your message was submitted!" is shown.
- The message appears at the top of the Message Board with "Admin" as Author, "message" as Message and the correct date as Date.
- A form for posting a message is shown.

---

## Test case 5.10: Failed submit as logged in user with tampered username

Make sure message posting in other users' names cannot happen by manipulating POST.

### Input:

- TC 6.1 - view POST Request
- Copy POST request along with session cookie but change View\MessageView::UserName to "hacker".
- Send tampered POST request.

### Output:

- Feedback "You can only save messages under your real username" is shown.

## Test case 6.1: View list of user's messages

Make sure we see a list of all our messages as logged in users.

### Input:

- Testcase 5.9, then follow "Account" link.

### Output:

- The text "Logged in", is shown.
- A link "Go to message board" is shown.
- A table of messages with columns "Message" and "Edit" is shown.
- The message posted in TC 5.9 is shown.

---

## Test case 6.2: View message edit page

Make sure we can access the page to edit our message.

### Input:

- Testcase 6.1
- Click the "Edit"-button next to a message.

### Output:

- A form for updating the message is shown
- A link "Account" is shown.
- The message is already filled in as input.
- Username is shown as "Admin".

---

## Test case 6.2: Edit a message

Make sure we can edit our message.

### Input:

- Testcase 6.2
- Add the word "edited" to the end of the message in the message field.
- Click the "Update" button.

### Output:

- A form for writing new messages is shown (TC 5.7)
- Feedback "Your message was updated!" is shown.
- The relevant message has been updated and can be seen at the top of the Message Board with the word "edited" at the end and the current time as Date.

## Test case 6.3: Failed access to edit page of other users' messages

Make sure we cannot access the edit page of other users' messages.

### Input:

- TC 6.1 - view POST Request
- Copy POST request but change MessageId to 6.
- Send tampered POST request.

### Output:

- Feedback "You cannot edit other people's messages!" is shown.

---

## Test case 6.4: Failed submit when trying editing other users' messages

Make sure we cannot send POST requests to edit other users' messages.

### Input:

- TC 6.2 - view POST Request
- Copy POST request but change MessageId to 6.
- Send tampered POST request.

### Output:

- Feedback "You cannot edit other people's messages!" is shown.

---

## Test case 7.1: Delete a message

Make sure we can delete our own messages.

### Input:

- Testcase 6.1
- Click the "Delete"-button next to a message.

### Output:

- Feedback "Your message was removed!" is shown.
- A form for posting a message is shown.

---

## Test case 7.2: Unauthorized delete of other users' messages

Make sure we can't delete other users' messages.

### Input:

- TC 7.1 - view POST Request
- Copy POST request but change MessageId to 6.
- Send tampered POST request.

### Output:

- Feedback "You cannot edit other people's messages!" is shown.
