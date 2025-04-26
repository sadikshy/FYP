$(document).ready(function(){
    // Auto-dismiss profile alert
    setTimeout(function(){
        $("#profileAlert").alert('close');
    }, 5000);
    
    // Auto-dismiss password alert
    setTimeout(function(){
        $("#passwordAlert").alert('close');
    }, 5000);
    
    // Auto-dismiss order cancellation alert
    setTimeout(function(){
        $("#orderCancelAlert").alert('close');
    }, 5000);
    
    // Function to preview the selected image
    $("#profile_image").change(function() {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            
            // Display the file name next to the Choose File button
            var fileName = this.files[0].name;
            $(this).next('.file-name').remove(); // Remove any existing filename display
            $(this).after('<span class="file-name">' + fileName + '</span>');
            
            reader.onload = function(e) {
                // Update the profile preview in the edit form
                $("#account-profile-preview").attr('src', e.target.result);
                
                // Also update the profile image in the user info section at the top
                $(".user-info .account-profile-image").attr('src', e.target.result);
                
                // Update the small circular icon in the header if it exists
                if($("#profile-image").length) {
                    $("#profile-image").attr('src', e.target.result);
                }
            }
            
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    // Update cancel timers
    updateCancelTimers();
    
    // Set interval to update cancel timers every second
    setInterval(updateCancelTimers, 1000);
});

// Function to update all cancel timers on the page
function updateCancelTimers() {
    $('.cancel-timer').each(function() {
        var timeLeft = parseInt($(this).attr('data-time-left'));
        if (timeLeft > 0) {
            timeLeft--;
            $(this).attr('data-time-left', timeLeft);
            
            var minutes = Math.floor(timeLeft / 60);
            var seconds = timeLeft % 60;
            $(this).text('Time remaining: ' + minutes + ':' + (seconds < 10 ? '0' : '') + seconds);
        } else {
            $(this).text('Time expired');
            // Disable the cancel button
            $(this).closest('form').find('button[name="cancel_order"]').prop('disabled', true);
        }
    });
}