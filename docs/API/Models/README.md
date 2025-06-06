# Models

[API Documentation](../README.md) > Models

Domain models representing OpenFGA entities like stores, tuples, and authorization models.

**Total Components:** 73

## Subdirectories

| Directory | Description |
|-----------|-------------|
| [`Collections`](./Collections/README.md) | Type-safe collections for managing groups of domain objects. |
| [`Enums`](./Enums/README.md) | Enumeration types for consistent value constraints across the SDK. |

## Interfaces

| Name | Description |
|------|-------------|
| [`AssertionInterface`](./AssertionInterface.md) | Represents an assertion used to test authorization model correctness. Assertions are test cases t... |
| [`AssertionTupleKeyInterface`](./AssertionTupleKeyInterface.md) | Defines the contract for assertion tuple keys used in authorization model testing. An assertion t... |
| [`AuthorizationModelInterface`](./AuthorizationModelInterface.md) | Represents an OpenFGA authorization model that defines permission structures. Authorization model... |
| [`BatchCheckItemInterface`](./BatchCheckItemInterface.md) | Represents a single item in a batch check request. Each batch check item contains a tuple key to ... |
| [`BatchCheckSingleResultInterface`](./BatchCheckSingleResultInterface.md) | Represents the result of a single check within a batch check response. Each result contains wheth... |
| [`BatchTupleOperationInterface`](./BatchTupleOperationInterface.md) | Interface for batch tuple operations. Defines the contract for organizing tuple writes and delete... |
| [`BatchTupleResultInterface`](./BatchTupleResultInterface.md) | Interface for batch tuple operation results. Defines the contract for tracking and analyzing the ... |
| [`ComputedInterface`](./ComputedInterface.md) | Represents a computed userset in OpenFGA authorization models. Computed usersets allow you to def... |
| [`ConditionInterface`](./ConditionInterface.md) | Represents a condition that enables dynamic authorization in OpenFGA. Conditions allow OpenFGA to... |
| [`ConditionMetadataInterface`](./ConditionMetadataInterface.md) | Defines metadata information for conditions in OpenFGA authorization models. ConditionMetadata pr... |
| [`ConditionParameterInterface`](./ConditionParameterInterface.md) | Defines a parameter type for use in authorization conditions. ConditionParameter represents the t... |
| [`DifferenceV1Interface`](./DifferenceV1Interface.md) | Defines a difference operation between two usersets in authorization models. DifferenceV1 represe... |
| [`LeafInterface`](./LeafInterface.md) | Represents a leaf node in OpenFGA's userset tree structure. Leaf nodes are terminal nodes in the ... |
| [`MetadataInterface`](./MetadataInterface.md) | Represents metadata associated with OpenFGA authorization model components. Metadata provides add... |
| [`ModelInterface`](./ModelInterface.md) | Base interface for all OpenFGA model objects. This interface establishes the foundation for all d... |
| [`NodeInterface`](./NodeInterface.md) | Represents a node in a userset tree structure. Nodes are fundamental building blocks in OpenFGA's... |
| [`NodeUnionInterface`](./NodeUnionInterface.md) | Represents a union operation between multiple nodes in a userset tree. A node union combines mult... |
| [`ObjectRelationInterface`](./ObjectRelationInterface.md) | Represents an object-relation pair in OpenFGA authorization models. Object-relation pairs are fun... |
| [`RelationMetadataInterface`](./RelationMetadataInterface.md) | Represents metadata associated with a relation in OpenFGA authorization models. Relation metadata... |
| [`RelationReferenceInterface`](./RelationReferenceInterface.md) | Defines the contract for relation references with optional conditions. A relation reference speci... |
| [`SourceInfoInterface`](./SourceInfoInterface.md) | Represents source file information for OpenFGA model elements. Source information provides debugg... |
| [`StoreInterface`](./StoreInterface.md) | Represents an OpenFGA store that contains authorization models and relationship tuples. A store i... |
| [`TupleChangeInterface`](./TupleChangeInterface.md) | Represents a change event for a relationship tuple in OpenFGA. Tuple changes capture the history ... |
| [`TupleInterface`](./TupleInterface.md) | Represents a relationship tuple in the OpenFGA authorization system. Tuples are the fundamental b... |
| [`TupleKeyInterface`](./TupleKeyInterface.md) | Represents a tuple key that defines the components of a relationship in OpenFGA. Tuple keys are t... |
| [`TupleToUsersetV1Interface`](./TupleToUsersetV1Interface.md) | Defines a tuple-to-userset operation in authorization models. TupleToUsersetV1 represents an auth... |
| [`TypeDefinitionInterface`](./TypeDefinitionInterface.md) | Represents a type definition in an OpenFGA authorization model. Type definitions are the building... |
| [`TypedWildcardInterface`](./TypedWildcardInterface.md) | Defines the contract for typed wildcard specifications. A typed wildcard represents "all users of... |
| [`UserInterface`](./UserInterface.md) | Represents a user in an OpenFGA authorization model. In OpenFGA, users are flexible entities that... |
| [`UserObjectInterface`](./UserObjectInterface.md) | Represents a user object in OpenFGA authorization model. User objects are typed entities that can... |
| [`UserTypeFilterInterface`](./UserTypeFilterInterface.md) | Represents a filter for limiting users by their relationships to specific object types. User type... |
| [`UsersListUserInterface`](./UsersListUserInterface.md) | Represents a user in a list context for authorization operations. UsersListUser provides a simple... |
| [`UsersetInterface`](./UsersetInterface.md) | Defines the contract for userset specifications in authorization models. A userset represents a c... |
| [`UsersetTreeDifferenceInterface`](./UsersetTreeDifferenceInterface.md) | Defines a difference operation node in authorization evaluation trees. UsersetTreeDifference repr... |
| [`UsersetTreeInterface`](./UsersetTreeInterface.md) | Defines a tree structure for representing complex userset operations. UsersetTree provides a hier... |
| [`UsersetTreeTupleToUsersetInterface`](./UsersetTreeTupleToUsersetInterface.md) | Defines a tuple-to-userset operation node in authorization evaluation trees. UsersetTreeTupleToUs... |
| [`UsersetUserInterface`](./UsersetUserInterface.md) | Defines the contract for userset user specifications. A userset user represents a reference to us... |

## Classes

| Name | Description |
|------|-------------|
| [`Assertion`](./Assertion.md) | Tests whether your authorization model behaves correctly for specific scenarios. Assertions are t... |
| [`AssertionTupleKey`](./AssertionTupleKey.md) | Represents a tuple key used for testing authorization model assertions. An AssertionTupleKey defi... |
| [`AuthorizationModel`](./AuthorizationModel.md) | Defines the authorization rules and relationships for your application. An AuthorizationModel is ... |
| [`BatchCheckItem`](./BatchCheckItem.md) | Represents a single item in a batch check request. Each batch check item contains a tuple key to ... |
| [`BatchCheckSingleResult`](./BatchCheckSingleResult.md) | Represents the result of a single check within a batch check response. Each result contains wheth... |
| [`BatchTupleOperation`](./BatchTupleOperation.md) | Represents a batch tuple operation containing both writes and deletes. This model organizes tuple... |
| [`BatchTupleResult`](./BatchTupleResult.md) | Represents the result of a batch tuple operation. This model tracks the results of processing a b... |
| [`Computed`](./Computed.md) | Represents a computed userset reference in authorization evaluation trees. A Computed defines a u... |
| [`Condition`](./Condition.md) | Represents an ABAC (Attribute-Based Access Control) condition in your authorization model. A Cond... |
| [`ConditionMetadata`](./ConditionMetadata.md) | Contains metadata information about conditions in your authorization model. ConditionMetadata pro... |
| [`ConditionParameter`](./ConditionParameter.md) | Represents a parameter type definition for ABAC conditions. ConditionParameter defines the type s... |
| [`DifferenceV1`](./DifferenceV1.md) | Represents a set difference operation between two usersets. In authorization models, you sometime... |
| [`Leaf`](./Leaf.md) | Represents a leaf node in authorization evaluation trees containing specific users. A Leaf is a t... |
| [`Metadata`](./Metadata.md) | Contains metadata information about type definitions in your authorization model. Metadata provid... |
| [`Node`](./Node.md) | Represents a node in the authorization evaluation tree structure. When OpenFGA evaluates complex ... |
| [`NodeUnion`](./NodeUnion.md) | Represents a union of multiple nodes in an authorization model tree. When OpenFGA evaluates compl... |
| [`ObjectRelation`](./ObjectRelation.md) | Represents a reference to a specific relation on an object. In authorization models, you often ne... |
| [`RelationMetadata`](./RelationMetadata.md) | Contains metadata information about a relation in your authorization model. RelationMetadata prov... |
| [`RelationReference`](./RelationReference.md) | Represents a reference to a specific relation with optional conditions. A RelationReference ident... |
| [`SourceInfo`](./SourceInfo.md) | Represents source file information for debugging and development tools. SourceInfo provides metad... |
| [`Store`](./Store.md) | Represents an OpenFGA authorization store that contains your permission data. A Store is a contai... |
| [`Tuple`](./Tuple.md) | Represents a stored relationship tuple in your authorization system. A Tuple is a relationship re... |
| [`TupleChange`](./TupleChange.md) | Represents a change to a relationship tuple in your authorization store. When you modify relation... |
| [`TupleKey`](./TupleKey.md) | Represents a relationship tuple key defining a connection between user, relation, and object. A T... |
| [`TupleToUsersetV1`](./TupleToUsersetV1.md) | Represents a tuple-to-userset relationship that derives permissions from related objects. This en... |
| [`TypeDefinition`](./TypeDefinition.md) | Represents a type definition in your authorization model. A TypeDefinition defines an object type... |
| [`TypedWildcard`](./TypedWildcard.md) | Represents a wildcard that matches all users of a specific type. In authorization models, you som... |
| [`User`](./User.md) | Represents a user or user specification in authorization contexts. A User can represent different... |
| [`UserObject`](./UserObject.md) | Represents a specific user object with type and identifier. A UserObject provides a structured wa... |
| [`UserTypeFilter`](./UserTypeFilter.md) | Represents a filter for limiting users by type and optional relation. UserTypeFilter allows you t... |
| [`UsersListUser`](./UsersListUser.md) | Represents a user entry in a users list response. UsersListUser provides a simple wrapper around ... |
| [`Userset`](./Userset.md) | Represents a userset specification for computing groups of users. A Userset defines how to comput... |
| [`UsersetTree`](./UsersetTree.md) | Represents the evaluation tree for determining user access. When OpenFGA evaluates whether a user... |
| [`UsersetTreeDifference`](./UsersetTreeDifference.md) | Represents a difference operation node in authorization evaluation trees. UsersetTreeDifference c... |
| [`UsersetTreeTupleToUserset`](./UsersetTreeTupleToUserset.md) | Represents a tuple-to-userset operation node in authorization evaluation trees. UsersetTreeTupleT... |
| [`UsersetUser`](./UsersetUser.md) | Represents a user reference through a userset relationship. UsersetUser defines a user specificat... |

---

[← Back to API Documentation](../README.md)
