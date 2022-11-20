# README

How to Run

### How do I get set up?

- `composer install`
- copy schema file to project root
- add templates directory to project root
- run `php FormGenerator.php forms` to convert all tables into forms
- run `php FormGenerator.php forms [name]` to convert just one table
- converted forms appear in templates directory as html files

### TODO

- [ ] Params for file types and file locations
- [ ] Params for CSS frameworks (currently supports Bootstrap, looking at Tailwind)
<!--
- [ ] Marking fields that are foreign-key
- [ ] Select boxes using foreign-keys
- [ ] Field Lengths (size/scale)
- [ ] Default Values -->
