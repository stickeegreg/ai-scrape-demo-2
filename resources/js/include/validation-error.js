export class ValidationError extends Error {
    errors;

    constructor(validationData) {
        super(validationData.message);
        this.errors = validationData.errors;
    }
}
