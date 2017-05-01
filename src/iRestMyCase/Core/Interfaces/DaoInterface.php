<?php


namespace iRestMyCase\Core\Interfaces;

interface DaoInterface
{
	/**
	 * Returns the DAO Name
	 * @return string
	 */
	public function getName(): string;

	/**
	 * Create a persistent entry for the provided model instance
	 * @param $modelInstance
	 * @return mixed
	 */
	public function create($modelInstance);

	/**
	 * Read From Data Source using properties of the provided model instance
	 * @param $modelInstance
	 * @return mixed
	 */
	public function read($modelInstance);

	/**
	 * Update Data Source to match current model instance properties
	 * @param $model
	 * @return mixed
	 */
	public function update($model);

	/**
	 * Delete Data Source Representation matching the provided model's Primaty Key
	 * @param $modelInstance object
	 * @return mixed
	 */
	public function delete($modelInstance);

	/**
	 * Returns the List of Available Models for this DAO.
	 * This is used to populate the Model Generator list
	 *
	 * @return array
	 */
	public function getAvailableModelNames(): array;
}
