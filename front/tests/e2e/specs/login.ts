// https://docs.cypress.io/api/introduction/api.html

import data from "../var";

describe("My First Test", () => {
  it("should login properly", () => {
    cy.visit("/");
    cy.contains("h1.brand", "SAPIO CARDS").should("exist");
    cy.contains(
      "h1.brand span",
      "Learn efficiently with the flash card technique"
    ).should("exist");
    cy.get("#user > input").type(data.email);
    cy.get("#password > input").type(data.password);
    cy.get("#login").click();
    // Redirect to my decks
    cy.get("#nav-my-decks.router-link-active").should("exist");
  });

  it("should logout properly", () => {
    cy.visit("/");

    // Login
    cy.get("#user > input").type(data.email);
    cy.get("#password > input").type(data.password);
    cy.get("#login").click();
    cy.get("#nav-logout").should("exist");

    // Logout
    cy.get("#nav-logout").click();
    cy.get("#nav-logout").should("not.exist");
  });

  it("should display wrong username message", () => {
    cy.visit("/");
    const wrongEmail = "wrong@email.com";
    cy.get("#user > input").type(wrongEmail);
    cy.get("#password > input").type(data.password);
    cy.get("#login").click();
    cy.get("#error")
      .contains("User " + wrongEmail + " not found")
      .should("exist");
  });

  it("should display wrong password message", () => {
    cy.visit("/");
    cy.get("#user > input").type(data.email);
    cy.get("#password > input").type("wrong password");
    cy.get("#login").click();
    cy.get("#error")
      .contains("Wrong password")
      .should("exist");
  });

  it("should display not verified account message", () => {
    cy.visit("/");
    cy.get("#user > input").type(data.notVerifiedEmail);
    cy.get("#password > input").type(data.password);
    cy.get("#login").click();
    cy.get("#error")
      .contains("Account is not verified, check your emails.")
      .should("exist");
  });
});
