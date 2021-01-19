@acceptance-back
Feature: An event is raised when simple product is created

  Scenario:
    Given Julia has all the required permissions to create a product
    When Julia creates a simple product in the product grid
    Then an event is raised and added to the Events API message queue

#    Given everything is set up to launch a product import thanks to an import profile
#    And Julia has permission to launch product import jobs
#    When Julia imports a product
#    Then an event is raised and added to the Events API message queue
#
#    Given Julia has all the required permissions to create a product
#    When Julia duplicates a simple product in the UI
#    Then an event is raised and added to the Events API message queue
#
#    Given the connection user group has all the required permissions to create a product
#    When the user of the connection creates a product through the REST API
#    Then an event is raised and added to the Events API message queue


