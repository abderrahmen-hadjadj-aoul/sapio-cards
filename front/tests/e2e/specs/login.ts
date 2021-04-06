// https://docs.cypress.io/api/introduction/api.html

const email = "unit.test@unit.test";
const notVerifiedEmail = "not.verified@not.verified";
const password = "test";

describe("My First Test", () => {
  it("should login properly", () => {
    cy.visit("/");
    cy.contains("h1.brand", "SAPIO CARDS");
    cy.contains(
      "h1.brand span",
      "Learn efficiently with the flash card technique"
    );
    cy.get("#user > input").type(email);
    cy.get("#password > input").type(password);
    cy.get("#login").click();
  });

  it("should logout properly", () => {
    cy.visit("/");

    // Login
    cy.get("#user > input").type(email);
    cy.get("#password > input").type(password);
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
    cy.get("#password > input").type(password);
    cy.get("#login").click();
    cy.get("#error").contains("User " + wrongEmail + " not found");
  });

  it("should display wrong password message", () => {
    cy.visit("/");
    cy.get("#user > input").type(email);
    cy.get("#password > input").type("wrong password");
    cy.get("#login").click();
    cy.get("#error").contains("Wrong password");
  });

  it.only("should display not verified account message", () => {
    cy.visit("/");
    cy.get("#user > input").type(notVerifiedEmail);
    cy.get("#password > input").type(password);
    cy.get("#login").click();
    cy.get("#error").contains("Account is not verified, check your emails.");
  });
});
