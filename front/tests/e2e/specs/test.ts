// https://docs.cypress.io/api/introduction/api.html

describe("My First Test", () => {
  it("Visits the app root url", () => {
    cy.visit("/");
    cy.contains("h1.brand", "SAPIO CARDS");
    cy.contains(
      "h1.brand span",
      "Learn efficiently with the flash card technique"
    );
    cy.get("#user > input").type("unit.test@unit.test");
    cy.get("#password > input").type("test");
    cy.get("#login").click();
  });
});
