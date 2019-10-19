# Additional Requirement Specification

Editor: Jim Disenstam

## Secure Authentication component for the web

In addition to the system and use cases 1-4 described in [The original Requirement specification](https://github.com/dntoll/1dv610/blob/master/assignments/A2_resources/UseCases.md), this document outlines additional use cases.

## UC5 Post message to message board

### Main scenario

1. Starts when a user wants to view the message board.
2. System shows message board with authors and messages.
3. System asks for username and message.
4. User provides username and message.
5. System saves the message and displays in the message board along with other messages.

### Alternate Scenarios

- 3a. User is authenticated.
  1. The system shows the username instead of asking for it.
  2. User provides message.
  3. Step 5 in main scenario.
- 4a. User inputs a username already registered in the system.
  1.  System presents an error message.
  2.  Step 3 in main scenario.

## UC6 Editing a message

### Preconditions

A user is authenticated. Ex. UC1, UC3.

### Main scenario

1. Starts when a logged in user wants to edit a message.
2. The system presents all of the user's messages with an option to edit a message.
3. User tells the system he wants to edit a message.
4. The system shows the message and allows the user to update it.
5. User updates message.
6. System shows the updated message along with all other messages on the message board.

## UC7 Deleting a message

### Preconditions

A user is authenticated. Ex. UC1, UC3.

### Main scenario

1. Starts when a logged in user wants to delete a message.
2. The system presents all of the user's messages with an option to delete a message.
3. User tells the system he wants to delete a message.
4. System confirms the removal and shows a list of all remaining messages.
