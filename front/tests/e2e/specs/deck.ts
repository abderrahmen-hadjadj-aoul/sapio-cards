import utils from "../utils";

describe("Deck", () => {
  it("should create new deck", () => {
    utils.login(cy);
    const name = "some name " + Math.random();
    const description = "some description " + Math.random();
    cy.get("#create-new-deck-button").should("exist");
    cy.get("#create-new-deck-button").click();
    cy.get("h1:not(.brand)")
      .contains("Create new deck")
      .should("exist");
    cy.get("#name").type(name);
    cy.get("#description").type(description);
    cy.get("#create-button").click();
    cy.get(".deck h1").contains("Deck: " + name);
    cy.get("#description-text")
      .contains(description)
      .should("exist");
    cy.get(".deck")
      .invoke("attr", "data-test-id")
      .then(id => {
        cy.get("#nav-my-decks").click();
        cy.get(`.deck[data-test-id="${id}"] a span.name`)
          .last()
          .contains(name)
          .should("exist");
      });
  });

  it("should create new deck with empty description", () => {
    utils.login(cy);
    const name = "some name " + Math.random();
    cy.get("#create-new-deck-button").click();
    cy.get("#name").type(name);
    cy.get("#create-button").click();
    cy.get(".deck h1").contains("Deck: " + name);
    cy.get(".deck")
      .invoke("attr", "data-test-id")
      .then(id => {
        cy.get("#nav-my-decks").click();
        cy.get(`.deck[data-test-id="${id}"] a span.name`)
          .last()
          .contains(name);
      });
  });

  it("should display an error message for empty name", () => {
    utils.login(cy);
    const description = "some description " + Math.random();
    cy.get("#create-new-deck-button").click();
    cy.get("#description").type(description);
    cy.get("#create-button").click();
    cy.get("#error").contains("Name can NOT be empty");
  });

  it("should update deck name and description", () => {
    utils.login(cy);
    // Create Deck
    const name = "some new name " + Math.random();
    const description = "some new description " + Math.random();
    cy.get("#create-new-deck-button").should("exist");
    cy.get("#create-new-deck-button").click();
    cy.get("h1:not(.brand)").contains("Create new deck");
    cy.get("#name").type(name);
    cy.get("#description").type(description);
    cy.get("#create-button").click();
    // Update deck
    const newName = "some name " + Math.random();
    const newDescription = "some description " + Math.random();
    cy.get("#update-deck-button").click();
    cy.get("#name")
      .clear()
      .type(newName);
    cy.get("#description")
      .clear()
      .type(newDescription);
    cy.get("#update-modal button")
      .contains("OK")
      .click();
    cy.get("#description-text")
      .contains(newDescription)
      .should("exist");
    cy.get("#update-modal").should("not.be.visible");
    cy.get(".at-notification")
      .contains("Deck updated successfully")
      .should("exist");
  });

  it("should display an error message for empty name", () => {
    utils.login(cy);
    // Create Deck
    const name = "some new name " + Math.random();
    const description = "some new description " + Math.random();
    cy.get("#create-new-deck-button").should("exist");
    cy.get("#create-new-deck-button").click();
    cy.get("h1:not(.brand)").contains("Create new deck");
    cy.get("#name").type(name);
    cy.get("#description").type(description);
    cy.get("#create-button").click();
    // Update deck
    const newDescription = "some description " + Math.random();
    cy.get("#update-deck-button").click();
    cy.get("#name").clear();
    cy.get("#description")
      .clear()
      .type(newDescription);
    cy.get("#edit-deck-ok-button")
      .last()
      .contains("OK")
      .click();
    cy.get("#deck-error")
      .contains("Name can NOT be empty")
      .should("exist");
  });

  it("should publish deck", () => {
    utils.login(cy);
    // Create Deck
    const name = "some new name " + Math.random();
    const description = "some new description " + Math.random();
    cy.get("#create-new-deck-button").should("exist");
    cy.get("#create-new-deck-button").click();
    cy.get("h1:not(.brand)").contains("Create new deck");
    cy.get("#name").type(name);
    cy.get("#description").type(description);
    cy.get("#create-button").click();
    // Publish
    cy.get("#publications")
      .contains("Publications: 0")
      .should("exist");
    cy.get("#publish-button").click();
    cy.get("#publications")
      .contains("Publications: 1")
      .should("exist");
    cy.get(".at-notification")
      .contains("Deck successfully published")
      .should("exist");
    // Check in list
    cy.get("#nav-browse").click();
    cy.get(".deck")
      .contains(name)
      .should("exist");
    cy.get(".deck")
      .contains(name)
      .parent(".deck")
      .find("span.version")
      .contains("Version 1")
      .should("exist");
  });
});
