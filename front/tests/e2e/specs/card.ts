import utils from "../utils";

describe("Card", () => {
  it("should create new card", () => {
    utils.login(cy);
    // Create Deck
    const name = "some name " + Math.random();
    const description = "some description " + Math.random();
    cy.get("#create-new-deck-button").click();
    cy.get("#name").type(name);
    cy.get("#description").type(description);
    cy.get("#create-button").click();
    // Create card
    const question = "some question " + Math.random();
    const answer = "some answer " + Math.random();
    cy.get("#question").type(question);
    cy.get("#answer").type(answer);
    cy.get("#create-card-button").click();
    cy.get("td.index")
      .contains(1)
      .should("exist");
    cy.get("td.question")
      .contains(question)
      .should("exist");
    cy.get("td.answer")
      .contains(answer)
      .should("exist");
  });

  it("should display an error message for empty question", () => {
    utils.login(cy);
    // Create Deck
    const name = "some name " + Math.random();
    const description = "some description " + Math.random();
    cy.get("#create-new-deck-button").click();
    cy.get("#name").type(name);
    cy.get("#description").type(description);
    cy.get("#create-button").click();
    // Create card
    const answer = "some answer " + Math.random();
    cy.get("#answer").type(answer);
    cy.get("#create-card-button").click();
    cy.get("#error-card")
      .contains("Question can NOT be empty.")
      .should("exist");
  });

  it("should display an error message for empty answer", () => {
    utils.login(cy);
    // Create Deck
    const name = "some name " + Math.random();
    const description = "some description " + Math.random();
    cy.get("#create-new-deck-button").click();
    cy.get("#name").type(name);
    cy.get("#description").type(description);
    cy.get("#create-button").click();
    // Create card
    const question = "some question " + Math.random();
    cy.get("#question").type(question);
    cy.get("#create-card-button").click();
    cy.get("#error-card")
      .contains("Answer can NOT be empty.")
      .should("exist");
  });

  it("should display an error message for empty question and answer", () => {
    utils.login(cy);
    // Create Deck
    const name = "some name " + Math.random();
    const description = "some description " + Math.random();
    cy.get("#create-new-deck-button").click();
    cy.get("#name").type(name);
    cy.get("#description").type(description);
    cy.get("#create-button").click();
    // Create card
    cy.get("#create-card-button").click();
    cy.get("#error-card")
      .contains("Answer can NOT be empty.")
      .should("exist");
    cy.get("#error-card")
      .contains("Question can NOT be empty.")
      .should("exist");
  });

  it.only("should display an error message for empty question in modal", () => {
    utils.login(cy);
    // Create Deck
    const name = "some name " + Math.random();
    const description = "some description " + Math.random();
    cy.get("#create-new-deck-button").click();
    cy.get("#name").type(name);
    cy.get("#description").type(description);
    cy.get("#create-button").click();
    // Create card
    const question = "some question " + Math.random();
    const answer = "some answer " + Math.random();
    cy.get("#question").type(question);
    cy.get("#answer").type(answer);
    cy.get("#create-card-button").click();
    // Edit card
    cy.get(".edit-card-button").click();
    cy.get("#modal-question").clear();
    cy.get("#modal-answer").clear();
    cy.get("#edit-card-ok-button").click();
    cy.get("#error-edit-card-modal")
      .contains("Question can NOT be empty")
      .should("exist");
    cy.get("#error-edit-card-modal")
      .contains("Answer can NOT be empty")
      .should("exist");
  });
});
