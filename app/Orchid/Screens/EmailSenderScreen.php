<?php

namespace App\Orchid\Screens;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Relation;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;


class EmailSenderScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'subject' => date('F').'camping news' 
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Email sender';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    

    public function description(): ?string
    {
        return "Tool that sends ad-hoc email messages.";
    }

        public function layout(): array
    {
        return [
            Layout::rows([
                input::make('subject')
                    ->title('subject')
                    ->required()
                    ->placeholder('message subject line')
                    ->help('enter the subject line for your message'),

                Relation::make('users.')
                    ->title('Recipients')
                    ->multiple()
                    ->required()
                    ->placeholder('Email addresses')
                    ->help('Enter the users that you would like to send this message to.')
                    ->fromModel(User::class,'name','email'),

                quill::make('content')
                    ->title('content')
                    ->required()
                    ->placeholder('insert text here...')
                    ->help('Add the content for the message that you would like to send.'),


                        ])
            ];
    }

    public function sendMassage(requst $requst)
    {
        $requst->validate([
            'subject' => 'required|min:6|max:50',
            'users'   => 'required',
            'content' => 'required|min:10'
        ]);

        mail::raw($requst->get('content'),function (message $message) use($requst) {
            $message->form('sample@email.com');
            $message->subject($requst-get('subject'));

            foreach ($requst->get('users') as $email) {
                $message->to($email);
            }
        });
        Alert::info('Your email message has been sent successfully.');
    }

}
