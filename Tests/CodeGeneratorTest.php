<?php
/***************************************************************
 *  Copyright notice
 *
*  (c) 2010 Nico de Haen
 *  All rights reserved
 *
 *  This class is a backport of the corresponding class of FLOW3.
 *  All credits go to the v5 team.
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once('BaseRoundTripTestCase.php');

/**
 * 
 * @author Nico de Haen
 *
 */
class Tx_ExtbaseKickstarter_CodeGeneratorTest extends Tx_ExtbaseKickstarter_BaseRoundTripTestCase {
	
	function setUp(){
		parent::setUp();
	}
	
	/**
	 * Write a simple model class for a non aggregate root domain object with one boolean property
	 * @test
	 */
	function writeModelClassWithBooleanProperty(){
		$modelName = 'Model2';
		$propertyName = 'blue';
		$domainObject = $this->buildDomainObject($modelName);
		$property = new Tx_ExtbaseKickstarter_Domain_Model_DomainObject_BooleanProperty();
		$property->setName($propertyName);
		$property->setRequired(TRUE);
		$domainObject->addProperty($property);
		$classFileContent = $this->codeGenerator->generateDomainObjectCode($domainObject,$this->extension);
		
		$modelClassDir =  'Classes/Domain/Model/';
		$result = t3lib_div::mkdir_deep($this->extension->getExtensionDir(),$modelClassDir);
		$absModelClassDir = $this->extension->getExtensionDir().$modelClassDir;
		$this->assertTrue(is_dir($absModelClassDir),'Directory ' . $absModelClassDir . ' was not created');
		
		$modelClassPath =  $absModelClassDir . $domainObject->getName() . '.php';
		t3lib_div::writeFile($modelClassPath,$classFileContent);
		$this->assertFileExists($modelClassPath,'File was not generated: ' . $modelClassPath);
		$className = $domainObject->getClassName();
		include($modelClassPath);
		$this->assertTrue(class_exists($className),'Class was not generated:'.$className);
		
		$reflection = new ReflectionClass($className);
		$this->assertTrue($reflection->hasMethod('get' . ucfirst($propertyName)),'Getter was not generated');
		$this->assertTrue($reflection->hasMethod('set' . ucfirst($propertyName)),'Setter was not generated');
		$this->assertTrue($reflection->hasMethod('is' . ucfirst($propertyName)),'isMethod was not generated');
		$setterMethod = $reflection->getMethod('set' . ucfirst($propertyName));
		$parameters = $setterMethod->getParameters();
		$this->assertTrue((count($parameters) == 1),'Wrong parameter count in setter method');
		$parameter = current($parameters);
		$this->assertTrue(($parameter->getName() == $propertyName),'Wrong parameter name in setter method');
		
		unlink($modelClassPath);
	}

	/**
	 * Write a simple model class for a non aggregate root domain object with one string property
	 * @test
	 */
	function writeModelClassWithStringProperty(){
		$modelName = 'Model3';
		$propertyName = 'title';
		$domainObject = $this->buildDomainObject($modelName);
		$property = new Tx_ExtbaseKickstarter_Domain_Model_DomainObject_StringProperty();
		$property->setName($propertyName);
		//$property->setRequired(TRUE);
		$domainObject->addProperty($property);
		$classFileContent = $this->codeGenerator->generateDomainObjectCode($domainObject,$this->extension);
		
		$modelClassDir =  'Classes/Domain/Model/';
		$result = t3lib_div::mkdir_deep($this->extension->getExtensionDir(),$modelClassDir);
		$absModelClassDir = $this->extension->getExtensionDir().$modelClassDir;
		$this->assertTrue(is_dir($absModelClassDir),'Directory ' . $absModelClassDir . ' was not created');
		
		$modelClassPath =  $absModelClassDir . $domainObject->getName() . '.php';
		t3lib_div::writeFile($modelClassPath,$classFileContent);
		$this->assertFileExists($modelClassPath,'File was not generated: ' . $modelClassPath);
		$className = $domainObject->getClassName();
		include($modelClassPath);
		$this->assertTrue(class_exists($className),'Class was not generated:'.$className);
		
		$reflection = new ReflectionClass($className);
		$this->assertTrue($reflection->hasMethod('get' . ucfirst($propertyName)),'Getter was not generated');
		$this->assertTrue($reflection->hasMethod('set' . ucfirst($propertyName)),'Setter was not generated');
		$this->assertFalse($reflection->hasMethod('is' . ucfirst($propertyName)),'isMethod should not be generated');
		$setterMethod = $reflection->getMethod('set' . ucfirst($propertyName));
		$parameters = $setterMethod->getParameters();
		$this->assertTrue((count($parameters) == 1),'Wrong parameter count in setter method');
		$parameter = current($parameters);
		$this->assertTrue(($parameter->getName() == $propertyName),'Wrong parameter name in setter method');
		
		unlink($modelClassPath);
	}
	
	/**
	 * Write a simple model class for a non aggregate root domain object with one to one relation
	 * @test
	 */
	function writeModelClassWithZeroToOneRelation(){
		$modelName = 'Model4';
		$relatedModelName = 'relatedModel';
		$propertyName = 'relName';
		$domainObject = $this->buildDomainObject($modelName);
		$relatedDomainObject = $this->buildDomainObject($relatedModelName);
		$relation = new Tx_ExtbaseKickstarter_Domain_Model_DomainObject_Relation_ZeroToOneRelation();
		$relation->setName($propertyName);
		$relation->setForeignClass($relatedDomainObject);
		$domainObject->addProperty($relation);
		$classFileContent = $this->codeGenerator->generateDomainObjectCode($domainObject,$this->extension);
		
		$modelClassDir =  'Classes/Domain/Model/';
		$result = t3lib_div::mkdir_deep($this->extension->getExtensionDir(),$modelClassDir);
		$absModelClassDir = $this->extension->getExtensionDir().$modelClassDir;
		$this->assertTrue(is_dir($absModelClassDir),'Directory ' . $absModelClassDir . ' was not created');
		
		$modelClassPath =  $absModelClassDir . $domainObject->getName() . '.php';
		t3lib_div::writeFile($modelClassPath,$classFileContent);
		$this->assertFileExists($modelClassPath,'File was not generated: ' . $modelClassPath);
		$className = $domainObject->getClassName();
		include($modelClassPath);
		$this->assertTrue(class_exists($className),'Class was not generated:'.$className);
		
		$reflection = new Tx_ExtbaseKickstarter_Reflection_ClassReflection($className);
		$this->assertTrue($reflection->hasMethod('get' . ucfirst($propertyName)),'Getter was not generated');
		$this->assertTrue($reflection->hasMethod('set' . ucfirst($propertyName)),'Setter was not generated');
		$setterMethod = $reflection->getMethod('set' . ucfirst($propertyName));
		$this->assertTrue($setterMethod->isTaggedWith('param'),'No param tag set for setter method');
		$paramTagValues = $setterMethod->getTagValues('param');
		t3lib_div::devlog('Parameter','kickstarter',0,$paramTagValues);
		$this->assertTrue((strpos($paramTagValues[0],$relatedDomainObject->getClassName()) === 0),'Wrong param tag:'.$paramTagValues[0]);
		
		$parameters = $setterMethod->getParameters();
		$this->assertTrue((count($parameters) == 1),'Wrong parameter count in setter method');
		$parameter = current($parameters);
		$this->assertTrue(($parameter->getName() == $propertyName),'Wrong parameter name in setter method');
		$this->assertTrue(($parameter->getTypeHint() == $relatedDomainObject->getClassName()),'Wrong type hint for setter parameter:'.$parameter->getTypeHint());
		
		unlink($modelClassPath);
	}
	
/**
	 * Write a simple model class for a non aggregate root domain object with one to one relation
	 * @test
	 */
	function writeModelClassWithZeroToManyRelation(){
		$modelName = 'Model5';
		$relatedModelName = 'relatedModel';
		$propertyName = 'relNames';
		$domainObject = $this->buildDomainObject($modelName);
		$relatedDomainObject = $this->buildDomainObject($relatedModelName);
		$relation = new Tx_ExtbaseKickstarter_Domain_Model_DomainObject_Relation_ZeroToManyRelation();
		$relation->setName($propertyName);
		$relation->setForeignClass($relatedDomainObject);
		$domainObject->addProperty($relation);
		
		$classFileContent = $this->codeGenerator->generateDomainObjectCode($domainObject,$this->extension);
		
		$modelClassDir =  'Classes/Domain/Model/';
		$result = t3lib_div::mkdir_deep($this->extension->getExtensionDir(),$modelClassDir);
		$absModelClassDir = $this->extension->getExtensionDir().$modelClassDir;
		$this->assertTrue(is_dir($absModelClassDir),'Directory ' . $absModelClassDir . ' was not created');
		
		$modelClassPath =  $absModelClassDir . $domainObject->getName() . '.php';
		t3lib_div::writeFile($modelClassPath,$classFileContent);
		$this->assertFileExists($modelClassPath,'File was not generated: ' . $modelClassPath);
		$className = $domainObject->getClassName();
		include($modelClassPath);
		$this->assertTrue(class_exists($className),'Class was not generated:'.$className);
		
		$reflection = new Tx_ExtbaseKickstarter_Reflection_ClassReflection($className);
		$this->assertTrue($reflection->hasMethod('add' . ucfirst(Tx_ExtbaseKickstarter_Utility_Inflector::singularize($propertyName))),'Add method was not generated');
		$this->assertTrue($reflection->hasMethod('remove' . ucfirst(Tx_ExtbaseKickstarter_Utility_Inflector::singularize($propertyName))),'Remove method was not generated');
		$this->assertTrue($reflection->hasMethod('set' . ucfirst($propertyName)),'Setter was not generated');
		$this->assertTrue($reflection->hasMethod('set' . ucfirst($propertyName)),'Setter was not generated');
		
		//checking methods
		$setterMethod = $reflection->getMethod('set' . ucfirst($propertyName));
		$this->assertTrue($setterMethod->isTaggedWith('param'),'No param tag set for setter method');
		$paramTagValues = $setterMethod->getTagValues('param');
		$this->assertTrue((strpos($paramTagValues[0],'Tx_Extbase_Persistence_ObjectStorage<' . $relatedDomainObject->getClassName()) === 0),'Wrong param tag:'.$paramTagValues[0]);
		
		$parameters = $setterMethod->getParameters();
		$this->assertTrue((count($parameters) == 1),'Wrong parameter count in setter method');
		$parameter = current($parameters);
		$this->assertTrue(($parameter->getName() == $propertyName),'Wrong parameter name in setter method');
		$this->assertTrue(($parameter->getTypeHint() == 'Tx_Extbase_Persistence_ObjectStorage'),'Wrong type hint for setter parameter:'.$parameter->getTypeHint());
		
		$addMethod = $reflection->getMethod('add' . ucfirst(Tx_ExtbaseKickstarter_Utility_Inflector::singularize($propertyName)));
		$this->assertTrue($addMethod->isTaggedWith('param'),'No param tag set for setter method');
		$paramTagValues = $addMethod->getTagValues('param');
		$this->assertTrue((strpos($paramTagValues[0],$relatedDomainObject->getClassName()) === 0),'Wrong param tag:'.$paramTagValues[0]);
		
		$parameters = $addMethod->getParameters();
		$this->assertTrue((count($parameters) == 1),'Wrong parameter count in add method');
		$parameter = current($parameters);
		$this->assertTrue(($parameter->getName() == Tx_ExtbaseKickstarter_Utility_Inflector::singularize($propertyName)),'Wrong parameter name in add method');
		$this->assertTrue(($parameter->getTypeHint() == $relatedDomainObject->getClassName()),'Wrong type hint for add method parameter:'.$parameter->getTypeHint());
		
		$removeMethod = $reflection->getMethod('remove' . ucfirst(Tx_ExtbaseKickstarter_Utility_Inflector::singularize($propertyName)));
		$this->assertTrue($removeMethod->isTaggedWith('param'),'No param tag set for remove method');
		$paramTagValues = $removeMethod->getTagValues('param');
		$this->assertTrue((strpos($paramTagValues[0],$relatedDomainObject->getClassName()) === 0),'Wrong param tag:'.$paramTagValues[0]);
		
		$parameters = $removeMethod->getParameters();
		$this->assertTrue((count($parameters) == 1),'Wrong parameter count in remove method');
		$parameter = current($parameters);
		$this->assertTrue(($parameter->getName() == Tx_ExtbaseKickstarter_Utility_Inflector::singularize($propertyName).'ToRemove'),'Wrong parameter name in remove method');
		$this->assertTrue(($parameter->getTypeHint() ==  $relatedDomainObject->getClassName()),'Wrong type hint for remove method parameter:'.$parameter->getTypeHint());
		unlink($modelClassPath);
	}
	
	/**
	 * Write a simple model class for a non aggregate root domain object
	 * @test
	 */
	function writeSimpleControllerClassFromDomainObject(){
		$domainObject = $this->buildDomainObject('Model6');
		
		$classFileContent = $this->codeGenerator->generateActionControllerCode($domainObject,$this->extension);
		
		$controllerClassDir =  'Classes/Controller/';
		$result = t3lib_div::mkdir_deep($this->extension->getExtensionDir(),$controllerClassDir);
		$absControllerClassDir = $this->extension->getExtensionDir().$controllerClassDir;
		$this->assertTrue(is_dir($absControllerClassDir),'Directory ' . $absControllerClassDir . ' was not created');
		
		$controllerClassPath =  $absControllerClassDir . $domainObject->getName() . 'Controller.php';
		t3lib_div::writeFile($controllerClassPath,$classFileContent);
		$this->assertFileExists($controllerClassPath,'File was not generated: ' . $controllerClassPath);
		$className = $domainObject->getControllerName();
		include($controllerClassPath);
		$this->assertTrue(class_exists($className),'Class was not generated:'.$className);
		
		unlink($controllerClassPath);
	}
	
	/**
	 * This test is definitely too generic, since it creates the required classes 
	 * with a whole codeGenerator->build call
	 * 
	 * @test
	 */
	function writeAggregateRootClassesFromDomainObject(){
		$domainObject = $this->buildDomainObject('Model1',true,true);
		$property = new Tx_ExtbaseKickstarter_Domain_Model_DomainObject_BooleanProperty();
		$property->setName('blue');
		$property->setRequired(TRUE);
		$domainObject->addProperty($property);
		
		$this->extension->addDomainObject($domainObject);
		
		$result = $this->codeGenerator->build($this->extension);
		
		$this->assertEquals($result,'success',$result);
		
		$this->assertFileExists($this->extension->getExtensionDir().'Classes/Domain/Model/'. $domainObject->getName() . '.php');
		$this->assertFileExists($this->extension->getExtensionDir().'Classes/Domain/Repository/'. $domainObject->getName() . 'Repository.php');
		$this->assertFileExists($this->extension->getExtensionDir().'Classes/Controller/'. $domainObject->getName() . 'Controller.php');
		
		t3lib_div::rmdir($this->extension->getExtensionDir().'Classes',true);
	}

}

?>