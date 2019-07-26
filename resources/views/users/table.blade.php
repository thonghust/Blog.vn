<div class="table-responsive">
    <table class="table" id="users-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $labels = ['warning', 'danger', 'info', 'success', 'default']; ?>
            @foreach($users as $user)
            <tr>
                <td>{!! $user->name !!}</td>
                <td>{!! $user->email !!}</td>
                <td>
                    @foreach($user->roles()->get() as $role)
                    <span class="label label-{!! $labels[ $role->id % 5 ] !!}">{!! $role->display_name !!}</span>
                    @endforeach
                </td>
                <td>
                    {!! Form::open(['route' => ['users.destroy', $user->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{!! route('users.show', [$user->id]) !!}" class='btn btn-default'><i class="glyphicon glyphicon-eye-open"></i> View</a>
                        <a href="{!! route('users.edit', [$user->id]) !!}" class='btn btn-default'><i class="glyphicon glyphicon-edit"></i> Change</a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i> Delete', ['type' => 'submit', 'class' => 'btn btn-danger', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
