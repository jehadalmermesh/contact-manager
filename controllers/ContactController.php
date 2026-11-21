<?php
class ContactController extends Controller
{
    private Contact $contacts;

    public function __construct()
    {
        $this->contacts = new Contact();
    }

    /** List is public; the add/edit/delete buttons only show for logged-in users. */
    public function index(): void
    {
        $this->view('contacts/index', ['contacts' => $this->contacts->all()]);
    }

    public function create(): void
    {
        Auth::requireLogin();
        $this->view('contacts/form', [
            'title'   => 'Add contact',
            'action'  => url('contacts/store'),
            'contact' => ['name' => '', 'email' => '', 'phone' => ''],
            'errors'  => [],
        ]);
    }

    public function store(): void
    {
        Auth::requireLogin();
        $this->requirePost();

        [$data, $errors] = Contact::validate($_POST);
        if (!$errors && $this->contacts->emailTaken($data['email'])) {
            $errors['email'] = 'A contact with this email already exists.';
        }
        if ($errors) {
            $this->view('contacts/form', ['title' => 'Add contact', 'action' => url('contacts/store'), 'contact' => $data, 'errors' => $errors]);
            return;
        }

        $this->contacts->create($data);
        flash('Contact added.');
        redirect('contacts');
    }

    public function edit(): void
    {
        Auth::requireLogin();
        $contact = $this->findOr404();
        $this->view('contacts/form', [
            'title'   => 'Edit contact',
            'action'  => url('contacts/update', ['id' => $contact['id']]),
            'contact' => $contact,
            'errors'  => [],
        ]);
    }

    public function update(): void
    {
        Auth::requireLogin();
        $this->requirePost();
        $contact = $this->findOr404();

        [$data, $errors] = Contact::validate($_POST);
        if (!$errors && $this->contacts->emailTaken($data['email'], (int) $contact['id'])) {
            $errors['email'] = 'A contact with this email already exists.';
        }
        if ($errors) {
            $this->view('contacts/form', [
                'title'   => 'Edit contact',
                'action'  => url('contacts/update', ['id' => $contact['id']]),
                'contact' => $data,
                'errors'  => $errors,
            ]);
            return;
        }

        $this->contacts->update((int) $contact['id'], $data);
        flash('Contact updated.');
        redirect('contacts');
    }

    /** Delete is POST-only with a CSRF token, so a link or image tag can't trigger it. */
    public function destroy(): void
    {
        Auth::requireLogin();
        $this->requirePost();
        $contact = $this->findOr404();
        $this->contacts->delete((int) $contact['id']);
        flash('Contact deleted.');
        redirect('contacts');
    }

    private function findOr404(): array
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $contact = $id ? $this->contacts->find($id) : null;
        if (!$contact) {
            $this->notFound();
        }
        return $contact;
    }
}
